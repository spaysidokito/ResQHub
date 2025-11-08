<?php

namespace App\Services;

use App\Models\Disaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TyphoonTrackingService
{
    public function updateTyphoonPositions(): int
    {
        $typhoons = Disaster::where('type', 'typhoon')
            ->where('status', 'active')
            ->get();

        $count = 0;
        foreach ($typhoons as $typhoon) {
            if ($this->updateTyphoonPosition($typhoon)) {
                $count++;
            }
        }

        return $count;
    }

    private function updateTyphoonPosition(Disaster $typhoon): bool
    {
        $currentLat = $typhoon->latitude;
        $currentLon = $typhoon->longitude;

        $movementSpeed = $typhoon->movement_speed ?? rand(10, 25);
        $movementDirection = $typhoon->movement_direction ?? $this->getRandomDirection();

        $windSpeed = $this->calculateWindSpeed($typhoon);
        $pressure = $this->calculatePressure($windSpeed);

        $newPosition = $this->calculateNewPosition(
            $currentLat,
            $currentLon,
            $movementSpeed,
            $movementDirection
        );

        if (!$this->isValidPosition($newPosition['lat'], $newPosition['lon'])) {
            $typhoon->update(['status' => 'resolved']);
            return false;
        }

        $locationName = $this->getLocationName($newPosition['lat'], $newPosition['lon']);
        $description = $this->generateDescription($windSpeed, $movementSpeed, $movementDirection, $pressure);

        $typhoon->update([
            'latitude' => $newPosition['lat'],
            'longitude' => $newPosition['lon'],
            'wind_speed' => $windSpeed,
            'wind_direction' => $this->getWindDirection(),
            'movement_direction' => $movementDirection,
            'movement_speed' => $movementSpeed,
            'pressure' => $pressure,
            'last_updated' => now(),
            'location' => $locationName,
            'description' => $description,
        ]);

        return true;
    }

    private function calculateNewPosition(float $lat, float $lon, float $speed, string $direction): array
    {
        $distanceKm = $speed * 0.1;
        $earthRadius = 6371;

        $bearing = $this->directionToBearing($direction);
        $bearingRad = deg2rad($bearing);

        $latRad = deg2rad($lat);
        $lonRad = deg2rad($lon);

        $newLatRad = asin(
            sin($latRad) * cos($distanceKm / $earthRadius) +
            cos($latRad) * sin($distanceKm / $earthRadius) * cos($bearingRad)
        );

        $newLonRad = $lonRad + atan2(
            sin($bearingRad) * sin($distanceKm / $earthRadius) * cos($latRad),
            cos($distanceKm / $earthRadius) - sin($latRad) * sin($newLatRad)
        );

        return [
            'lat' => rad2deg($newLatRad),
            'lon' => rad2deg($newLonRad),
        ];
    }

    private function directionToBearing(string $direction): float
    {
        return match (strtoupper($direction)) {
            'N' => 0,
            'NNE' => 22.5,
            'NE' => 45,
            'ENE' => 67.5,
            'E' => 90,
            'ESE' => 112.5,
            'SE' => 135,
            'SSE' => 157.5,
            'S' => 180,
            'SSW' => 202.5,
            'SW' => 225,
            'WSW' => 247.5,
            'W' => 270,
            'WNW' => 292.5,
            'NW' => 315,
            'NNW' => 337.5,
            default => 0,
        };
    }

    private function getRandomDirection(): string
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        return $directions[array_rand($directions)];
    }

    private function getWindDirection(): string
    {
        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        return $directions[array_rand($directions)];
    }

    private function calculateWindSpeed(Disaster $typhoon): int
    {
        $baseSpeed = $typhoon->wind_speed ?? rand(65, 120);
        $variation = rand(-10, 15);
        $newSpeed = max(55, min(200, $baseSpeed + $variation));

        return $newSpeed;
    }

    private function calculatePressure(int $windSpeed): int
    {
        return max(900, 1013 - (int)($windSpeed * 0.8));
    }

    private function isValidPosition(float $lat, float $lon): bool
    {
        return $lat >= 0.0 && $lat <= 25.0 && $lon >= 110.0 && $lon <= 135.0;
    }

    private function getLocationName(float $latitude, float $longitude): string
    {
        if ($latitude >= 14.0 && $latitude <= 19.0 && $longitude >= 120.0 && $longitude <= 122.5) {
            return 'Luzon, Philippines';
        } elseif ($latitude >= 9.5 && $latitude <= 12.5 && $longitude >= 122.0 && $longitude <= 125.5) {
            return 'Visayas, Philippines';
        } elseif ($latitude >= 5.5 && $latitude <= 10.0 && $longitude >= 121.0 && $longitude <= 126.5) {
            return 'Mindanao, Philippines';
        } elseif ($latitude < 14.0 && $longitude < 120.0) {
            return 'West Philippine Sea';
        } elseif ($latitude > 19.0) {
            return 'Philippine Sea (North)';
        } else {
            return 'Philippine Area of Responsibility';
        }
    }

    private function generateDescription(int $windSpeed, float $movementSpeed, string $direction, int $pressure): string
    {
        $category = $this->getTyphoonCategory($windSpeed);

        return sprintf(
            '%s with maximum sustained winds of %d km/h. Moving %s at %d km/h. Central pressure: %d hPa. Residents in affected areas are advised to take necessary precautions.',
            $category,
            $windSpeed,
            $direction,
            (int)$movementSpeed,
            $pressure
        );
    }

    private function getTyphoonCategory(int $windSpeed): string
    {
        if ($windSpeed >= 185) {
            return 'Super Typhoon';
        } elseif ($windSpeed >= 118) {
            return 'Typhoon';
        } elseif ($windSpeed >= 89) {
            return 'Severe Tropical Storm';
        } elseif ($windSpeed >= 62) {
            return 'Tropical Storm';
        } else {
            return 'Tropical Depression';
        }
    }
}
