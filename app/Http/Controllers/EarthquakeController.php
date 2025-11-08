<?php

namespace App\Http\Controllers;

use App\Models\Earthquake;
use App\Services\EarthquakeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EarthquakeController extends Controller
{
    public function __construct(private EarthquakeService $earthquakeService)
    {
    }

    public function index()
    {
        return Inertia::render('Dashboard', [
            'earthquakes' => Earthquake::orderBy('occurred_at', 'desc')
                ->take(20)
                ->get(),
        ]);
    }

    public function list(Request $request)
    {
        $query = Earthquake::query()->orderBy('occurred_at', 'desc');

        if ($request->has('min_magnitude')) {
            $query->where('magnitude', '>=', $request->min_magnitude);
        }

        if ($request->has('days')) {
            $query->where('occurred_at', '>=', now()->subDays($request->days));
        }

        $earthquakes = $query->limit(100)->get();

        return response()->json([
            'earthquakes' => $earthquakes,
            'count' => $earthquakes->count(),
        ]);
    }

    public function show(Earthquake $earthquake)
    {
        return response()->json($earthquake->load('alerts'));
    }

    public function refresh()
    {
        $earthquakes = $this->earthquakeService->fetchRecentEarthquakes();

        foreach ($earthquakes as $earthquake) {
            $this->earthquakeService->checkAndCreateAlerts($earthquake);
        }

        return response()->json([
            'message' => 'Earthquake data refreshed successfully',
            'count' => count($earthquakes),
        ]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        $earthquakes = Earthquake::all()->filter(function ($earthquake) use ($request) {
            $distance = $this->earthquakeService->calculateDistance(
                $request->latitude,
                $request->longitude,
                $earthquake->latitude,
                $earthquake->longitude
            );
            return $distance <= $request->radius;
        })->values();

        return response()->json([
            'earthquakes' => $earthquakes,
            'count' => $earthquakes->count(),
        ]);
    }
}
