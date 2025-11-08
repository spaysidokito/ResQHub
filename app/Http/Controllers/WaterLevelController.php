<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class WaterLevelController extends Controller
{
    public function index()
    {
        $stations = Cache::remember('water_levels', 300, function () {
            return $this->fetchWaterLevels();
        });

        return response()->json([
            'stations' => $stations,
            'last_update' => now()->toIso8601String(),
        ]);
    }

    private function fetchWaterLevels()
    {
        $stations = [
            [
                'id' => 1,
                'name' => 'Angono',
                'location' => 'Laguna (Rizal)',
                'waterLevel' => ($wl1 = $this->getRandomWaterLevel(12, 14)),
                'status' => $this->determineStatus($wl1, 13.5, 15.0),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 15.0,
                'alertLevel' => 13.5,
            ],
            [
                'id' => 2,
                'name' => 'Burgos',
                'location' => 'Rizal (Rizal)',
                'waterLevel' => ($wl2 = $this->getRandomWaterLevel(26, 28)),
                'status' => $this->determineStatus($wl2, 28.0, 30.0),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 30.0,
                'alertLevel' => 28.0,
            ],
            [
                'id' => 3,
                'name' => 'La Mesa Dam',
                'location' => 'Quezon City',
                'waterLevel' => ($wl3 = $this->getRandomWaterLevel(77, 79)),
                'status' => $this->determineStatus($wl3, 79.0, 80.15),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 80.15,
                'alertLevel' => 79.0,
            ],
            [
                'id' => 4,
                'name' => 'Ipo Dam',
                'location' => 'Bulacan',
                'waterLevel' => ($wl4 = $this->getRandomWaterLevel(99, 101)),
                'status' => $this->determineStatus($wl4, 100.8, 101.85),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 101.85,
                'alertLevel' => 100.8,
            ],
            [
                'id' => 5,
                'name' => 'Marikina River',
                'location' => 'Marikina City',
                'waterLevel' => ($wl5 = $this->getRandomWaterLevel(14, 16)),
                'status' => $this->determineStatus($wl5, 16.0, 18.0),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 18.0,
                'alertLevel' => 16.0,
            ],
            [
                'id' => 6,
                'name' => 'Pasig River',
                'location' => 'Metro Manila',
                'waterLevel' => ($wl6 = $this->getRandomWaterLevel(1.5, 2.5)),
                'status' => $this->determineStatus($wl6, 3.0, 4.0),
                'trend' => $this->getRandomTrend(),
                'lastUpdate' => now()->format('n/j/Y, g:i:s A'),
                'criticalLevel' => 4.0,
                'alertLevel' => 3.0,
            ],
        ];

        return $stations;
    }

    private function getRandomWaterLevel($min, $max)
    {
        return round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 2);
    }

    private function getRandomTrend()
    {
        $trends = ['rising', 'falling', 'stable'];
        return $trends[array_rand($trends)];
    }

    private function determineStatus($current, $alert, $critical)
    {
        if ($current >= $critical) {
            return 'critical';
        } elseif ($current >= $alert) {
            return 'alert';
        } else {
            return 'normal';
        }
    }
}
