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
        $preferences = \App\Models\UserPreference::all();

        foreach ($preferences as $pref) {
            $distance = $this->calculateDistance(
                $earthquake->latitude,
                $earthquake->longitude,
                $pref->latitude,
                $pref->longitude
            );

            if ($distance <= $pref->radius_km && $earthquake->magnitude >= $pref->min_magnitude) {
                Alert::create([
                    'earthquake_id' => $earthquake->id,
                    'user_id' => $pref->user_id,
                    'type' => 'earthquake',
                    'severity' => $earthquake->severity,
                    'title' => "Magnitude {$earthquake->magnitude} Earthquake",
                    'message' => "A magnitude {$earthquake->magnitude} earthquake occurred at {$earthquake->location}, approximately " . round($distance) . " km from your location.",
                    'sent_at' => now(),
                ]);
            }
        }
    }
}
