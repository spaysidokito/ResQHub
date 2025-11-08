<?php

namespace Database\Seeders;

use App\Models\Disaster;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DisasterSeeder extends Seeder
{
    public function run(): void
    {
        $disasters = [
            // Floods
            [
                'external_id' => 'flood_ph_001',
                'type' => 'flood',
                'name' => 'Metro Manila Flash Flood',
                'description' => 'Heavy rainfall causing flash floods in low-lying areas of Metro Manila',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'location' => 'Metro Manila',
                'country' => 'Philippines',
                'severity' => 'high',
                'status' => 'active',
                'started_at' => Carbon::now()->subHours(6),
                'source' => 'PAGASA',
                'is_verified' => true,
            ],
            [
                'external_id' => 'flood_ph_002',
                'type' => 'flood',
                'name' => 'Cagayan Valley Flooding',
                'description' => 'River overflow affecting multiple municipalities',
                'latitude' => 17.6132,
                'longitude' => 121.7270,
                'location' => 'Cagayan Valley',
                'country' => 'Philippines',
                'severity' => 'moderate',
                'status' => 'monitoring',
                'started_at' => Carbon::now()->subHours(12),
                'source' => 'PAGASA',
                'is_verified' => true,
            ],

            // Typhoons
            [
                'external_id' => 'typhoon_ph_001',
                'type' => 'typhoon',
                'name' => 'Typhoon Uwan (Fung-wong)',
                'description' => 'Typhoon Uwan (International name: Fung-wong) approaching Northern Luzon with strong winds and heavy rainfall',
                'latitude' => 18.5000,
                'longitude' => 121.5000,
                'location' => 'Northern Luzon',
                'country' => 'Philippines',
                'severity' => 'critical',
                'status' => 'active',
                'started_at' => Carbon::now()->subHours(8),
                'source' => 'PAGASA',
                'is_verified' => true,
                'details' => [
                    'wind_speed' => '150 km/h',
                    'category' => 'Typhoon',
                    'direction' => 'West-Northwest',
                    'speed' => '25 km/h',
                    'international_name' => 'Fung-wong',
                ],
            ],
            [
                'external_id' => 'typhoon_ph_002',
                'type' => 'typhoon',
                'name' => 'Typhoon Pepito',
                'description' => 'Category 3 typhoon approaching Eastern Visayas',
                'latitude' => 11.2403,
                'longitude' => 125.0041,
                'location' => 'Eastern Visayas',
                'country' => 'Philippines',
                'severity' => 'critical',
                'status' => 'active',
                'started_at' => Carbon::now()->subDay(),
                'source' => 'PAGASA',
                'is_verified' => true,
                'details' => [
                    'wind_speed' => '185 km/h',
                    'category' => 3,
                    'direction' => 'Northwest',
                    'speed' => '20 km/h',
                ],
            ],
            [
                'external_id' => 'typhoon_ph_003',
                'type' => 'typhoon',
                'name' => 'Tropical Depression Ofel',
                'description' => 'Tropical depression forming east of Mindanao',
                'latitude' => 8.4542,
                'longitude' => 126.6319,
                'location' => 'East of Mindanao',
                'country' => 'Philippines',
                'severity' => 'moderate',
                'status' => 'monitoring',
                'started_at' => Carbon::now()->subHours(18),
                'source' => 'PAGASA',
                'is_verified' => true,
                'details' => [
                    'wind_speed' => '55 km/h',
                    'category' => 'TD',
                    'direction' => 'West',
                    'speed' => '15 km/h',
                ],
            ],

            // Fires
            [
                'external_id' => 'fire_ph_001',
                'type' => 'fire',
                'name' => 'Quezon City Residential Fire',
                'description' => 'Large fire affecting residential area',
                'latitude' => 14.6760,
                'longitude' => 121.0437,
                'location' => 'Quezon City',
                'country' => 'Philippines',
                'severity' => 'high',
                'status' => 'active',
                'started_at' => Carbon::now()->subHours(3),
                'source' => 'BFP',
                'is_verified' => true,
                'details' => [
                    'alarm_level' => 'Task Force Alpha',
                    'affected_structures' => 50,
                    'casualties' => 0,
                ],
            ],
            [
                'external_id' => 'fire_ph_002',
                'type' => 'fire',
                'name' => 'Cebu Grassland Fire',
                'description' => 'Wildfire spreading in grassland areas',
                'latitude' => 10.3157,
                'longitude' => 123.8854,
                'location' => 'Cebu Province',
                'country' => 'Philippines',
                'severity' => 'moderate',
                'status' => 'monitoring',
                'started_at' => Carbon::now()->subHours(8),
                'source' => 'BFP',
                'is_verified' => true,
            ],
            [
                'external_id' => 'fire_ph_003',
                'type' => 'fire',
                'name' => 'Davao Market Fire',
                'description' => 'Fire at public market, contained',
                'latitude' => 7.0731,
                'longitude' => 125.6128,
                'location' => 'Davao City',
                'country' => 'Philippines',
                'severity' => 'low',
                'status' => 'resolved',
                'started_at' => Carbon::now()->subDay(),
                'ended_at' => Carbon::now()->subHours(20),
                'source' => 'BFP',
                'is_verified' => true,
            ],

            // Additional disasters
            [
                'external_id' => 'flood_ph_003',
                'type' => 'flood',
                'name' => 'Laguna Lake Overflow',
                'description' => 'Rising water levels affecting lakeside communities',
                'latitude' => 14.3500,
                'longitude' => 121.2500,
                'location' => 'Laguna',
                'country' => 'Philippines',
                'severity' => 'moderate',
                'status' => 'active',
                'started_at' => Carbon::now()->subHours(10),
                'source' => 'PAGASA',
                'is_verified' => true,
            ],
        ];

        foreach ($disasters as $disaster) {
            Disaster::create($disaster);
        }

        $this->command->info('Created ' . count($disasters) . ' disaster records');
    }
}
