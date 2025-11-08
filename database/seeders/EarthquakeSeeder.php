<?php

namespace Database\Seeders;

use App\Models\Earthquake;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EarthquakeSeeder extends Seeder
{
    public function run(): void
    {
        $earthquakes = [
            [
                'external_id' => 'demo_eq_001',
                'magnitude' => 6.5,
                'location' => '15 km SE of Manila, Philippines',
                'latitude' => 14.4500,
                'longitude' => 121.1000,
                'depth' => 10,
                'occurred_at' => Carbon::now()->subHours(2),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_002',
                'magnitude' => 4.2,
                'location' => '8 km N of Quezon City, Philippines',
                'latitude' => 14.7000,
                'longitude' => 121.0300,
                'depth' => 15,
                'occurred_at' => Carbon::now()->subHours(5),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_003',
                'magnitude' => 5.8,
                'location' => '22 km W of Cebu City, Philippines',
                'latitude' => 10.3157,
                'longitude' => 123.6922,
                'depth' => 20,
                'occurred_at' => Carbon::now()->subHours(8),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_004',
                'magnitude' => 3.5,
                'location' => '12 km E of Davao City, Philippines',
                'latitude' => 7.0731,
                'longitude' => 125.6128,
                'depth' => 8,
                'occurred_at' => Carbon::now()->subHours(12),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_005',
                'magnitude' => 7.2,
                'location' => '45 km NW of Baguio City, Philippines',
                'latitude' => 16.4500,
                'longitude' => 120.5500,
                'depth' => 25,
                'occurred_at' => Carbon::now()->subDay(),
                'source' => 'USGS',
            ],
            [
                'external_id' => 'demo_eq_006',
                'magnitude' => 4.8,
                'location' => '18 km S of Iloilo City, Philippines',
                'latitude' => 10.5000,
                'longitude' => 122.5600,
                'depth' => 12,
                'occurred_at' => Carbon::now()->subDay()->subHours(6),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_007',
                'magnitude' => 5.2,
                'location' => '30 km E of Tacloban City, Philippines',
                'latitude' => 11.2433,
                'longitude' => 125.2714,
                'depth' => 18,
                'occurred_at' => Carbon::now()->subDays(2),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_008',
                'magnitude' => 3.8,
                'location' => '10 km NE of Zamboanga City, Philippines',
                'latitude' => 6.9214,
                'longitude' => 122.0790,
                'depth' => 10,
                'occurred_at' => Carbon::now()->subDays(3),
                'source' => 'PHIVOLCS',
            ],
            [
                'external_id' => 'demo_eq_009',
                'magnitude' => 6.1,
                'location' => '25 km SW of Legazpi City, Philippines',
                'latitude' => 13.1391,
                'longitude' => 123.7436,
                'depth' => 22,
                'occurred_at' => Carbon::now()->subDays(4),
                'source' => 'USGS',
            ],
            [
                'external_id' => 'demo_eq_010',
                'magnitude' => 4.5,
                'location' => '14 km W of Cagayan de Oro, Philippines',
                'latitude' => 8.4542,
                'longitude' => 124.6319,
                'depth' => 16,
                'occurred_at' => Carbon::now()->subDays(5),
                'source' => 'PHIVOLCS',
            ],
        ];

        foreach ($earthquakes as $earthquake) {
            Earthquake::create($earthquake);
        }

        $this->command->info('Created ' . count($earthquakes) . ' demo earthquakes');
    }
}
