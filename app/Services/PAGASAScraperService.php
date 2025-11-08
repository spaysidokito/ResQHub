<?php

namespace App\Services;

use App\Models\Disaster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class PAGASAScraperService
{
    private const PAGASA_TROPICAL_CYCLONE_URL = 'https://www.pagasa.dost.gov.ph/tropical-cyclone/severe-weather-bulletin';
    private const PAGASA_WEATHER_ADVISORY_URL = 'https://www.pagasa.dost.gov.ph/weather';
    private const PAGASA_FLOOD_URL = 'https://www.pagasa.dost.gov.ph/flood';

    public function scrapeTyphoonData(): int
    {
        $count = 0;

        try {
            $count += $this->scrapeTropicalCyclones();
        } catch (\Exception $e) {
            Log::error('Error scraping PAGASA tropical cyclones: ' . $e->getMessage());
        }

        try {
            $count += $this->scrapeWeatherAdvisories();
        } catch (\Exception $e) {
            Log::error('Error scraping PAGASA weather advisories: ' . $e->getMessage());
        }

        return $count;
    }

    private function scrapeTropicalCyclones(): int
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get(self::PAGASA_TROPICAL_CYCLONE_URL);

        if (!$response->successful()) {
            Log::warning('PAGASA tropical cyclone page not accessible');
            return $this->createDefaultTyphoonAdvisory();
        }

        $html = $response->body();
        $count = 0;

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $bulletins = $xpath->query("//div[contains(@class, 'bulletin')]");

        if ($bulletins->length === 0) {
            return $this->createDefaultTyphoonAdvisory();
        }

        foreach ($bulletins as $bulletin) {
            $typhoonData = $this->extractTyphoonData($bulletin, $xpath);

            if ($typhoonData) {
                $disaster = Disaster::updateOrCreate(
                    ['external_id' => $typhoonData['external_id']],
                    $typhoonData
                );

                if ($disaster->wasRecentlyCreated) {
                    $count++;
                }
            }
        }

        return $count > 0 ? $count : $this->createDefaultTyphoonAdvisory();
    }

    private function extractTyphoonData($bulletin, $xpath): ?array
    {
        try {
            $nameNode = $xpath->query(".//h3 | .//h2 | .//strong", $bulletin)->item(0);
            $name = $nameNode ? trim($nameNode->textContent) : null;

            if (!$name || !$this->isTyphoonName($name)) {
                return null;
            }

            $contentNode = $xpath->query(".//p | .//div[@class='content']", $bulletin)->item(0);
            $content = $contentNode ? $contentNode->textContent : '';

            $windSpeed = $this->extractWindSpeed($content);
            $location = $this->extractLocation($content);
            $movement = $this->extractMovement($content);
            $pressure = $this->extractPressure($content);

            $coordinates = $this->extractCoordinates($content);

            return [
                'external_id' => 'pagasa_tc_' . md5($name . date('Y-m-d')),
                'type' => 'typhoon',
                'name' => $this->cleanTyphoonName($name),
                'description' => $this->generateDescription($name, $windSpeed, $movement, $pressure),
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lon'],
                'location' => $location ?: 'Philippine Area of Responsibility',
                'country' => 'Philippines',
                'severity' => $this->determineSeverity($windSpeed),
                'status' => 'active',
                'wind_speed' => $windSpeed,
                'wind_direction' => $movement['wind_direction'] ?? 'Variable',
                'movement_direction' => $movement['direction'] ?? 'WNW',
                'movement_speed' => $movement['speed'] ?? 20,
                'pressure' => $pressure,
                'last_updated' => now(),
                'started_at' => now(),
                'source' => 'PAGASA',
            ];
        } catch (\Exception $e) {
            Log::error('Error extracting typhoon data: ' . $e->getMessage());
            return null;
        }
    }

    private function extractWindSpeed(string $content): int
    {
        if (preg_match('/(\d+)\s*km\/h/i', $content, $matches)) {
            return (int)$matches[1];
        }
        if (preg_match('/winds?\s+of\s+(\d+)/i', $content, $matches)) {
            return (int)$matches[1];
        }
        return 85;
    }

    private function extractLocation(string $content): ?string
    {
        $locations = [
            'Luzon', 'Visayas', 'Mindanao', 'Manila', 'Quezon', 'Bicol',
            'Cagayan', 'Isabela', 'Aurora', 'Zambales', 'Bataan', 'Pampanga',
            'Bulacan', 'Rizal', 'Cavite', 'Laguna', 'Batangas', 'Palawan'
        ];

        foreach ($locations as $location) {
            if (stripos($content, $location) !== false) {
                return $location . ', Philippines';
            }
        }

        return null;
    }

    private function extractMovement(string $content): array
    {
        $movement = [
            'direction' => 'WNW',
            'speed' => 20,
            'wind_direction' => 'NE',
        ];

        if (preg_match('/moving\s+([\w\-]+)\s+at\s+(\d+)/i', $content, $matches)) {
            $movement['direction'] = strtoupper($matches[1]);
            $movement['speed'] = (int)$matches[2];
        }

        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'WNW', 'NNW', 'ENE', 'ESE', 'SSE', 'SSW', 'WSW', 'NNE'];
        foreach ($directions as $dir) {
            if (stripos($content, $dir) !== false) {
                $movement['direction'] = $dir;
                break;
            }
        }

        return $movement;
    }

    private function extractPressure(string $content): int
    {
        if (preg_match('/(\d+)\s*hPa/i', $content, $matches)) {
            return (int)$matches[1];
        }
        if (preg_match('/pressure\s+(\d+)/i', $content, $matches)) {
            return (int)$matches[1];
        }
        return 980;
    }

    private function extractCoordinates(string $content): array
    {
        if (preg_match('/(\d+\.?\d*)[°\s]*N.*?(\d+\.?\d*)[°\s]*E/i', $content, $matches)) {
            return [
                'lat' => (float)$matches[1],
                'lon' => (float)$matches[2],
            ];
        }

        return [
            'lat' => 14.5995,
            'lon' => 120.9842,
        ];
    }

    private function isTyphoonName(string $name): bool
    {
        $keywords = ['typhoon', 'tropical', 'storm', 'depression', 'cyclone', 'signal'];

        foreach ($keywords as $keyword) {
            if (stripos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function cleanTyphoonName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);

        if (strlen($name) > 100) {
            $name = substr($name, 0, 100) . '...';
        }

        return $name;
    }

    private function determineSeverity(int $windSpeed): string
    {
        if ($windSpeed >= 185) return 'critical';
        if ($windSpeed >= 118) return 'critical';
        if ($windSpeed >= 89) return 'high';
        if ($windSpeed >= 62) return 'moderate';
        return 'low';
    }

    private function generateDescription(string $name, int $windSpeed, array $movement, int $pressure): string
    {
        $category = $this->getTyphoonCategory($windSpeed);

        return sprintf(
            '%s with maximum sustained winds of %d km/h. Moving %s at %d km/h. Central pressure: %d hPa. Residents in affected areas are advised to monitor updates and follow local authority instructions.',
            $category,
            $windSpeed,
            $movement['direction'] ?? 'WNW',
            $movement['speed'] ?? 20,
            $pressure
        );
    }

    private function getTyphoonCategory(int $windSpeed): string
    {
        if ($windSpeed >= 185) return 'Super Typhoon';
        if ($windSpeed >= 118) return 'Typhoon';
        if ($windSpeed >= 89) return 'Severe Tropical Storm';
        if ($windSpeed >= 62) return 'Tropical Storm';
        return 'Tropical Depression';
    }

    private function scrapeWeatherAdvisories(): int
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get(self::PAGASA_WEATHER_ADVISORY_URL);

        if (!$response->successful()) {
            return 0;
        }

        return 0;
    }

    private function createDefaultTyphoonAdvisory(): int
    {
        $externalId = 'pagasa_default_' . date('Y-m-d');

        $disaster = Disaster::updateOrCreate(
            ['external_id' => $externalId],
            [
                'type' => 'typhoon',
                'name' => 'Tropical Cyclone Advisory',
                'description' => 'No active tropical cyclone in the Philippine Area of Responsibility. PAGASA continues to monitor weather systems.',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'location' => 'Philippine Area of Responsibility',
                'country' => 'Philippines',
                'severity' => 'low',
                'status' => 'monitoring',
                'last_updated' => now(),
                'started_at' => now(),
                'source' => 'PAGASA',
            ]
        );

        return $disaster->wasRecentlyCreated ? 1 : 0;
    }
}
