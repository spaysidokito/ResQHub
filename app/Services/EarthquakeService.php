<?php

namespace App\Services;

use App\Models\Earthquake;
use App\Models\Alert;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EarthquakeService
{
    private const USGS_API = 'https://earthquake.usgs.gov/fdsnws/event/1/query';

    public function fetchRecentEarthquakes(int $days = 7, float $minMagnitude = 2.5)
    {
        try {
            $response = Http::timeout(30)->get(self::USGS_API, [
                'format' => 'geojson',
                'starttime' => Carbon::now()->subDays($days)->toIso8601String(),
                'minmagnitude' => $minMagnitude,
                'orderby' => 'time',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->processEarthquakeData($data);
            }

            Log::error('USGS API request failed', ['status' => $response->status()]);
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching earthquake data', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function processEarthquakeData(array $data): array
    {
        $earthquakes = [];

        foreach ($data['features'] ?? [] as $feature) {
            $properties = $feature['properties'];
            $geometry = $feature['geometry'];

            $earthquake = Earthquake::updateOrCreate(
                ['external_id' => $feature['id']],
                [
                    'magnitude' => $properties['mag'] ?? 0,
                    'location' => $properties['place'] ?? 'Unknown',
                    'latitude' => $geometry['coordinates'][1] ?? 0,
                    'longitude' => $geometry['coordinates'][0] ?? 0,
                    'depth' => $geometry['coordinates'][2] ?? 0,
                    'occurred_at' => Carbon::createFromTimestampMs($properties['time']),
                    'source' => 'USGS',
                    'details' => json_encode($properties),
                ]
            );

            $earthquakes[] = $earthquake;
        }

        return $earthquakes;
    }

    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function checkAndCreateAlerts(Earthquake $earthquake)
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            // Get user preferences or use defaults
            $preference = \App\Models\UserPreference::where('user_id', $user->id)->first();

            $userLat = $preference->latitude ?? 14.5995; // Default: Manila
            $userLon = $preference->longitude ?? 120.9842;
            $radiusKm = $preference->radius_km ?? 100; // Default: 100km
            $minMagnitude = $preference->min_magnitude ?? 3.0; // Default: 3.0

            // Calculate distance between user location and earthquake
            $distance = $this->calculateDistance(
                $earthquake->latitude,
                $earthquake->longitude,
                $userLat,
                $userLon
            );

            // Only create alert if earthquake is within user's radius and above minimum magnitude
            if ($distance <= $radiusKm && $earthquake->magnitude >= $minMagnitude) {
                // Check if alert already exists to avoid duplicates
                $existingAlert = Alert::where('earthquake_id', $earthquake->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existingAlert) {
                    Alert::create([
                        'earthquake_id' => $earthquake->id,
                        'user_id' => $user->id,
                        'severity' => $this->getSeverityFromMagnitude($earthquake->magnitude),
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Determine severity level based on earthquake magnitude
     */
    private function getSeverityFromMagnitude(float $magnitude): string
    {
        if ($magnitude >= 7.0) {
            return 'critical';
        } elseif ($magnitude >= 6.0) {
            return 'high';
        } elseif ($magnitude >= 4.5) {
            return 'moderate';
        } else {
            return 'low';
        }
    }
}
