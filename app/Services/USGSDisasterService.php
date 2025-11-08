<?php

namespace App\Services;

use App\Models\Disaster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class USGSDisasterService
{
    private const USGS_EARTHQUAKE_API = 'https://earthquake.usgs.gov/fdsnws/event/1/query';
    private const USGS_SIGNIFICANT_EVENTS = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_month.geojson';

    public function fetchSignificantEvents(): int
    {
        $count = 0;

        try {
            $count += $this->fetchSignificantEarthquakes();
        } catch (\Exception $e) {
            Log::error('Error fetching USGS significant events: ' . $e->getMessage());
        }

        return $count;
    }

    private function fetchSignificantEarthquakes(): int
    {
        $response = Http::timeout(30)->get(self::USGS_SIGNIFICANT_EVENTS);

        if (!$response->successful()) {
            Log::error('USGS significant events API request failed');
            return 0;
        }

        $data = $response->json();
        $count = 0;

        foreach ($data['features'] ?? [] as $feature) {
            $properties = $feature['properties'];
            $geometry = $feature['geometry'];

            $latitude = $geometry['coordinates'][1] ?? 0;
            $longitude = $geometry['coordinates'][0] ?? 0;

            if (!$this->isNearPhilippines($latitude, $longitude)) {
                continue;
            }

            $magnitude = $properties['mag'] ?? 0;

            if ($magnitude < 5.0) {
                continue;
            }

            $externalId = 'usgs_sig_' . $feature['id'];

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => 'earthquake',
                    'name' => 'M' . number_format($magnitude, 1) . ' Earthquake - ' . ($properties['place'] ?? 'Philippines'),
                    'description' => $this->generateEarthquakeDescription($properties, $geometry),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $this->getLocationName($latitude, $longitude),
                    'country' => 'Philippines',
                    'severity' => $this->getEarthquakeSeverity($magnitude),
                    'status' => 'active',
                    'last_updated' => now(),
                    'started_at' => Carbon::createFromTimestampMs($properties['time']),
                    'source' => 'USGS',
                ]
            );

            if ($disaster->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    private function generateEarthquakeDescription(array $properties, array $geometry): string
    {
        $magnitude = $properties['mag'] ?? 0;
        $depth = $geometry['coordinates'][2] ?? 0;
        $place = $properties['place'] ?? 'Unknown location';

        $impact = '';
        if ($magnitude >= 7.0) {
            $impact = 'Major earthquake. Severe damage expected. Tsunami possible.';
        } elseif ($magnitude >= 6.0) {
            $impact = 'Strong earthquake. Significant damage possible in populated areas.';
        } elseif ($magnitude >= 5.0) {
            $impact = 'Moderate earthquake. Minor damage possible near epicenter.';
        }

        return sprintf(
            'Magnitude %.1f earthquake detected at %s. Depth: %.1f km. %s Residents should check for damage and be prepared for aftershocks.',
            $magnitude,
            $place,
            $depth,
            $impact
        );
    }

    private function isNearPhilippines(float $latitude, float $longitude): bool
    {
        return $latitude >= 4.0 && $latitude <= 21.0 &&
               $longitude >= 116.0 && $longitude <= 127.0;
    }

    private function getLocationName(float $latitude, float $longitude): string
    {
        if ($latitude >= 14.0 && $latitude <= 19.0 && $longitude >= 120.0 && $longitude <= 122.5) {
            return 'Luzon, Philippines';
        } elseif ($latitude >= 9.5 && $latitude <= 12.5 && $longitude >= 122.0 && $longitude <= 125.5) {
            return 'Visayas, Philippines';
        } elseif ($latitude >= 5.5 && $latitude <= 10.0 && $longitude >= 121.0 && $longitude <= 126.5) {
            return 'Mindanao, Philippines';
        }

        return 'Philippines';
    }

    private function getEarthquakeSeverity(float $magnitude): string
    {
        if ($magnitude >= 7.0) return 'critical';
        if ($magnitude >= 6.0) return 'high';
        if ($magnitude >= 5.0) return 'moderate';
        return 'low';
    }
}
