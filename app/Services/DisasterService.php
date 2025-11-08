<?php

namespace App\Services;

use App\Models\Disaster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DisasterService
{
    private const EONET_API_URL = 'https://eonet.gsfc.nasa.gov/api/v3/events';
    private const GDACS_RSS_URL = 'https://www.gdacs.org/xml/rss.xml';
    private const USGS_EARTHQUAKE_API = 'https://earthquake.usgs.gov/fdsnws/event/1/query';
    private const PAGASA_WEATHER_URL = 'https://www.pagasa.dost.gov.ph/';

    public function fetchAndStoreDisasters(): int
    {
        $count = 0;

        try {
            $count += $this->fetchFromUSGS();
        } catch (\Exception $e) {
            Log::error('Error fetching from USGS: ' . $e->getMessage());
        }

        try {
            $pagasaScraper = app(\App\Services\PAGASAScraperService::class);
            $count += $pagasaScraper->scrapeTyphoonData();
        } catch (\Exception $e) {
            Log::error('Error scraping PAGASA: ' . $e->getMessage());
        }

        try {
            $usgsDisaster = app(\App\Services\USGSDisasterService::class);
            $count += $usgsDisaster->fetchSignificantEvents();
        } catch (\Exception $e) {
            Log::error('Error fetching USGS disasters: ' . $e->getMessage());
        }

        try {
            $count += $this->fetchFromGDACS();
        } catch (\Exception $e) {
            Log::error('Error fetching from GDACS: ' . $e->getMessage());
        }

        try {
            $count += $this->fetchFromEONET();
        } catch (\Exception $e) {
            Log::error('Error fetching from EONET: ' . $e->getMessage());
        }

        return $count;
    }

    private function fetchFromUSGS(): int
    {
        $response = Http::timeout(30)->get(self::USGS_EARTHQUAKE_API, [
            'format' => 'geojson',
            'starttime' => Carbon::now()->subDays(7)->toIso8601String(),
            'minlatitude' => 4.0,
            'maxlatitude' => 21.0,
            'minlongitude' => 116.0,
            'maxlongitude' => 127.0,
            'minmagnitude' => 3.0,
            'orderby' => 'time',
        ]);

        if (!$response->successful()) {
            Log::error('USGS API request failed', ['status' => $response->status()]);
            return 0;
        }

        $data = $response->json();
        $count = 0;

        foreach ($data['features'] ?? [] as $feature) {
            $properties = $feature['properties'];
            $geometry = $feature['geometry'];
            $magnitude = $properties['mag'] ?? 0;

            if ($magnitude < 3.0) {
                continue;
            }

            $externalId = 'usgs_eq_' . $feature['id'];
            $latitude = $geometry['coordinates'][1] ?? 0;
            $longitude = $geometry['coordinates'][0] ?? 0;

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => 'earthquake',
                    'name' => 'M' . number_format($magnitude, 1) . ' Earthquake - ' . ($properties['place'] ?? 'Philippines'),
                    'description' => 'Magnitude ' . $magnitude . ' earthquake detected at depth of ' . ($geometry['coordinates'][2] ?? 0) . ' km',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $this->getLocationName($latitude, $longitude),
                    'country' => 'Philippines',
                    'severity' => $this->getEarthquakeSeverity($magnitude),
                    'status' => 'active',
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

    private function fetchFromPAGASA(): int
    {
        $count = 0;

        $count += $this->fetchPAGASATyphoons();
        $count += $this->fetchPAGASAFloods();

        return $count;
    }

    private function fetchPAGASATyphoons(): int
    {
        $typhoons = [
            [
                'name' => 'Tropical Cyclone Advisory',
                'location' => 'Philippine Area of Responsibility',
                'severity' => 'moderate',
                'description' => 'Active tropical cyclone monitoring in Philippine waters',
            ],
        ];

        $count = 0;
        foreach ($typhoons as $typhoon) {
            $externalId = 'pagasa_tc_' . md5($typhoon['name'] . date('Y-m-d'));

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => 'typhoon',
                    'name' => $typhoon['name'],
                    'description' => $typhoon['description'],
                    'latitude' => 14.5995,
                    'longitude' => 120.9842,
                    'location' => $typhoon['location'],
                    'country' => 'Philippines',
                    'severity' => $typhoon['severity'],
                    'status' => 'monitoring',
                    'started_at' => now(),
                    'source' => 'PAGASA',
                ]
            );

            if ($disaster->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    private function fetchPAGASAFloods(): int
    {
        $floodAreas = [
            ['name' => 'Metro Manila', 'lat' => 14.5995, 'lon' => 120.9842],
            ['name' => 'Cagayan Valley', 'lat' => 17.6132, 'lon' => 121.7270],
            ['name' => 'Central Luzon', 'lat' => 15.4833, 'lon' => 120.7000],
        ];

        $count = 0;
        foreach ($floodAreas as $area) {
            $externalId = 'pagasa_flood_' . md5($area['name'] . date('Y-m-d'));

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => 'flood',
                    'name' => $area['name'] . ' Flood Advisory',
                    'description' => 'Flood monitoring and advisory for ' . $area['name'] . ' region',
                    'latitude' => $area['lat'],
                    'longitude' => $area['lon'],
                    'location' => $area['name'] . ', Philippines',
                    'country' => 'Philippines',
                    'severity' => 'low',
                    'status' => 'monitoring',
                    'started_at' => now(),
                    'source' => 'PAGASA',
                ]
            );

            if ($disaster->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    private function getEarthquakeSeverity(float $magnitude): string
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

    private function fetchFromEONET(): int
    {
        $response = Http::timeout(30)->get(self::EONET_API_URL, [
            'days' => 30,
            'status' => 'open',
        ]);

        if (!$response->successful()) {
            Log::error('EONET API request failed', ['status' => $response->status()]);
            return 0;
        }

        $data = $response->json();
        $count = 0;

        foreach ($data['events'] ?? [] as $event) {
            $categories = $event['categories'] ?? [];
            $type = $this->mapEONETCategory($categories[0]['id'] ?? '');

            if (!$type) {
                continue;
            }

            $geometry = $event['geometry'][0] ?? null;
            if (!$geometry) {
                continue;
            }

            $coordinates = $geometry['coordinates'] ?? [];
            $longitude = $coordinates[0] ?? 0;
            $latitude = $coordinates[1] ?? 0;

            if (!$this->isNearPhilippines($latitude, $longitude)) {
                continue;
            }

            $externalId = 'eonet_' . $event['id'];

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => $type,
                    'name' => $event['title'] ?? 'Unknown Event',
                    'description' => $event['description'] ?? '',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $this->getLocationName($latitude, $longitude),
                    'country' => 'Philippines',
                    'severity' => 'moderate',
                    'status' => 'active',
                    'started_at' => Carbon::parse($geometry['date'] ?? now()),
                    'source' => 'NASA EONET',
                ]
            );

            if ($disaster->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    private function fetchFromGDACS(): int
    {
        $response = Http::timeout(30)->get(self::GDACS_RSS_URL);

        if (!$response->successful()) {
            Log::error('GDACS RSS request failed', ['status' => $response->status()]);
            return 0;
        }

        $xml = simplexml_load_string($response->body());
        if (!$xml) {
            Log::error('Failed to parse GDACS RSS XML');
            return 0;
        }

        $count = 0;
        $namespaces = $xml->getNamespaces(true);

        foreach ($xml->channel->item as $item) {
            $gdacs = $item->children($namespaces['gdacs'] ?? 'gdacs', true);
            $geo = $item->children($namespaces['geo'] ?? 'geo', true);

            $eventType = strtolower((string)$gdacs->eventtype);
            $country = (string)$gdacs->country;

            if (!str_contains(strtolower($country), 'philippines') &&
                !str_contains(strtolower($country), 'manila')) {
                continue;
            }

            $type = $this->mapGDACSType($eventType);
            if (!$type) {
                continue;
            }

            $externalId = 'gdacs_' . md5((string)$item->link);
            $severity = $this->mapGDACSSeverity((string)$gdacs->alertlevel);

            $latLong = explode(' ', (string)$geo->Point->pos);
            $latitude = (float)($latLong[0] ?? 0);
            $longitude = (float)($latLong[1] ?? 0);

            $disaster = Disaster::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'type' => $type,
                    'name' => (string)$item->title,
                    'description' => strip_tags((string)$item->description),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $country,
                    'country' => 'Philippines',
                    'severity' => $severity,
                    'status' => 'active',
                    'started_at' => Carbon::parse((string)$item->pubDate),
                    'source' => 'GDACS',
                ]
            );

            if ($disaster->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    private function isNearPhilippines(float $latitude, float $longitude): bool
    {
        return $latitude >= 4.0 && $latitude <= 21.0 &&
               $longitude >= 116.0 && $longitude <= 127.0;
    }

    private function getLocationName(float $latitude, float $longitude): string
    {
        $regions = [
            ['name' => 'Luzon', 'lat' => [14.0, 19.0], 'lon' => [120.0, 122.5]],
            ['name' => 'Visayas', 'lat' => [9.5, 12.5], 'lon' => [122.0, 125.5]],
            ['name' => 'Mindanao', 'lat' => [5.5, 10.0], 'lon' => [121.0, 126.5]],
        ];

        foreach ($regions as $region) {
            if ($latitude >= $region['lat'][0] && $latitude <= $region['lat'][1] &&
                $longitude >= $region['lon'][0] && $longitude <= $region['lon'][1]) {
                return $region['name'] . ', Philippines';
            }
        }

        return 'Philippines';
    }

    private function mapEONETCategory(string $category): ?string
    {
        return match ($category) {
            'floods' => 'flood',
            'severeStorms', 'wildfires' => 'fire',
            default => null,
        };
    }

    private function mapGDACSType(string $type): ?string
    {
        return match (strtolower($type)) {
            'fl', 'flood' => 'flood',
            'tc', 'tropical cyclone', 'typhoon' => 'typhoon',
            'wf', 'wildfire', 'fire' => 'fire',
            'eq', 'earthquake' => 'earthquake',
            default => null,
        };
    }

    private function mapGDACSSeverity(string $alertLevel): string
    {
        return match (strtolower($alertLevel)) {
            'red' => 'critical',
            'orange' => 'high',
            'green' => 'moderate',
            default => 'low',
        };
    }
}
