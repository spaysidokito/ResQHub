<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisasterController extends Controller
{
    public function index()
    {
        $disasters = Disaster::where('country', 'Philippines')
            ->where('status', '!=', 'resolved')
            ->orderBy('severity', 'desc')
            ->orderBy('started_at', 'desc')
            ->take(50)
            ->get();

        $earthquakes = \App\Models\Earthquake::orderBy('occurred_at', 'desc')
            ->take(20)
            ->get();

        return Inertia::render('Dashboard', [
            'disasters' => $disasters,
            'earthquakes' => $earthquakes,
        ]);
    }

    public function list(Request $request)
    {
        $query = Disaster::query()->where('country', 'Philippines');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        $disasters = $query->orderBy('started_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'disasters' => $disasters,
            'count' => $disasters->count(),
        ]);
    }

    public function show(Disaster $disaster)
    {
        return response()->json($disaster->load('alerts'));
    }

    public function byType(string $type)
    {
        $disasters = Disaster::where('country', 'Philippines')
            ->where('type', $type)
            ->where('status', '!=', 'resolved')
            ->orderBy('severity', 'desc')
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'disasters' => $disasters,
            'type' => $type,
            'count' => $disasters->count(),
        ]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
            'type' => 'nullable|in:flood,typhoon,fire,earthquake',
        ]);

        $query = Disaster::where('country', 'Philippines')
            ->where('status', '!=', 'resolved');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $disasters = $query->get()->filter(function ($disaster) use ($request) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $disaster->latitude,
                $disaster->longitude
            );
            return $distance <= $request->radius;
        })->values();

        return response()->json([
            'disasters' => $disasters,
            'count' => $disasters->count(),
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
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
}
