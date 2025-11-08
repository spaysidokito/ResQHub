<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Earthquake;
use App\Models\Alert;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisasterManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_disasters' => Disaster::count(),
            'active_disasters' => Disaster::where('status', 'active')->count(),
            'total_earthquakes' => Earthquake::count(),
            'total_alerts' => Alert::count(),
            'unverified_disasters' => \App\Models\CitizenReport::where('status', 'pending')->count(),
        ];

        $recentDisasters = Disaster::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $disastersByType = Disaster::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        return view('admin.disasters', [
            'stats' => $stats,
            'recentDisasters' => $recentDisasters,
            'disastersByType' => $disastersByType,
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total_disasters' => Disaster::count(),
            'active_disasters' => Disaster::where('status', 'active')->count(),
            'total_earthquakes' => Earthquake::count(),
            'total_alerts' => Alert::count(),
            'unverified_disasters' => \App\Models\CitizenReport::where('status', 'pending')->count(),
        ];

        $recentDisasters = Disaster::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $disastersByType = Disaster::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        return response()->json([
            'stats' => $stats,
            'recentDisasters' => $recentDisasters,
            'disastersByType' => $disastersByType,
        ]);
    }

    public function create()
    {
        return view('admin.disasters-create');
    }

    public function verifyPage()
    {
        return view('admin.disasters-verify');
    }

    public function disasters(Request $request)
    {

        if ($request->wantsJson() || $request->ajax()) {
            $query = Disaster::query();

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('verified')) {
                $query->where('is_verified', $request->verified === 'true');
            }

            $disasters = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($disasters);
        }

        return view('admin.disasters-list');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_id' => 'required|string|unique:disasters',
            'type' => 'required|in:flood,typhoon,fire,earthquake',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'severity' => 'required|in:low,moderate,high,critical',
            'status' => 'required|in:active,monitoring,resolved',
            'details' => 'nullable|array',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            'source' => 'required|string|max:255',
        ]);

        $validated['is_verified'] = true; // Admin-created disasters are auto-verified

        $disaster = Disaster::create($validated);

        $this->createAlertsForDisaster($disaster);

        if (!$request->expectsJson()) {
            return redirect()->route('admin.disasters')
                ->with('success', 'Disaster "' . $disaster->name . '" created successfully!');
        }

        return response()->json([
            'message' => 'Disaster created successfully',
            'disaster' => $disaster,
        ], 201);
    }

    public function update(Request $request, Disaster $disaster)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'location' => 'sometimes|string|max:255',
            'severity' => 'sometimes|in:low,moderate,high,critical',
            'status' => 'sometimes|in:active,monitoring,resolved',
            'details' => 'nullable|array',
            'ended_at' => 'nullable|date',
            'is_verified' => 'sometimes|boolean',
        ]);

        $disaster->update($validated);

        return response()->json([
            'message' => 'Disaster updated successfully',
            'disaster' => $disaster,
        ]);
    }

    public function destroy(Disaster $disaster)
    {
        $isSimulation = $disaster->source === 'SIMULATION';
        $disaster->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => $isSimulation ? 'Simulation stopped successfully' : 'Disaster deleted successfully',
            ]);
        }

        return redirect()->route('admin.disasters')
            ->with('success', $isSimulation ? 'Simulation stopped successfully!' : 'Disaster deleted successfully!');
    }

    public function verify(Disaster $disaster)
    {
        $disaster->update(['is_verified' => true]);

        return response()->json([
            'message' => 'Disaster verified successfully',
            'disaster' => $disaster,
        ]);
    }

    public function resolve(Disaster $disaster)
    {
        $disaster->update([
            'status' => 'resolved',
            'ended_at' => now(),
        ]);

        return response()->json([
            'message' => 'Disaster marked as resolved',
            'disaster' => $disaster,
        ]);
    }

    public function stats()
    {
        $stats = [
            'disasters' => [
                'total' => Disaster::count(),
                'active' => Disaster::where('status', 'active')->count(),
                'monitoring' => Disaster::where('status', 'monitoring')->count(),
                'resolved' => Disaster::where('status', 'resolved')->count(),
            ],
            'by_type' => [
                'floods' => Disaster::where('type', 'flood')->count(),
                'typhoons' => Disaster::where('type', 'typhoon')->count(),
                'fires' => Disaster::where('type', 'fire')->count(),
                'earthquakes' => Earthquake::count(),
            ],
            'by_severity' => [
                'critical' => Disaster::where('severity', 'critical')->count(),
                'high' => Disaster::where('severity', 'high')->count(),
                'moderate' => Disaster::where('severity', 'moderate')->count(),
                'low' => Disaster::where('severity', 'low')->count(),
            ],
            'alerts' => [
                'total' => Alert::count(),
                'unread' => Alert::where('is_read', false)->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function simulate()
    {

        $types = ['flood', 'typhoon', 'fire', 'earthquake'];
        $severities = ['low', 'moderate', 'high', 'critical'];
        $locations = [
            ['name' => 'Metro Manila', 'lat' => 14.5995, 'lon' => 120.9842],
            ['name' => 'Cebu City', 'lat' => 10.3157, 'lon' => 123.8854],
            ['name' => 'Davao City', 'lat' => 7.0731, 'lon' => 125.6128],
            ['name' => 'Quezon City', 'lat' => 14.6760, 'lon' => 121.0437],
            ['name' => 'Baguio City', 'lat' => 16.4023, 'lon' => 120.5960],
        ];

        $type = $types[array_rand($types)];
        $severity = $severities[array_rand($severities)];
        $location = $locations[array_rand($locations)];

        $names = [
            'flood' => ['Flash Flood', 'River Overflow', 'Urban Flooding', 'Coastal Flooding'],
            'typhoon' => ['Typhoon Signal #3', 'Tropical Storm', 'Super Typhoon', 'Typhoon Warning'],
            'fire' => ['Residential Fire', 'Forest Fire', 'Industrial Fire', 'Grassland Fire'],
            'earthquake' => ['Seismic Activity', 'Earthquake Alert', 'Tremor Detected', 'Ground Shaking'],
        ];

        $disaster = Disaster::create([
            'external_id' => 'sim_' . uniqid(),
            'type' => $type,
            'name' => $names[$type][array_rand($names[$type])] . ' - SIMULATION',
            'description' => 'This is a simulated disaster for testing purposes. Created at ' . now()->format('Y-m-d H:i:s'),
            'latitude' => $location['lat'],
            'longitude' => $location['lon'],
            'location' => $location['name'],
            'country' => 'Philippines',
            'severity' => $severity,
            'status' => 'active',
            'started_at' => now(),
            'source' => 'SIMULATION',
            'is_verified' => true,
            'details' => [
                'simulated' => true,
                'created_by' => 'admin',
                'test_mode' => true,
            ],
        ]);

        $this->createAlertsForDisaster($disaster);

        return redirect()->route('admin.disasters')
            ->with('success', 'Simulated disaster created successfully! Check the dashboard to see it.');
    }

    
    public function simulateAlert()
    {

        $severities = ['low', 'moderate', 'high', 'critical'];
        $types = ['flood', 'typhoon', 'fire', 'earthquake'];

        $type = $types[array_rand($types)];
        $severity = $severities[array_rand($severities)];

        $disaster = Disaster::create([
            'external_id' => 'alert_test_' . uniqid(),
            'type' => $type,
            'name' => 'TEST ALERT - ' . strtoupper($type),
            'description' => 'This is a test alert for system testing purposes. Created at ' . now()->format('Y-m-d H:i:s'),
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'location' => 'Test Location',
            'country' => 'Philippines',
            'severity' => $severity,
            'status' => 'active',
            'started_at' => now(),
            'source' => 'TEST_ALERT',
            'is_verified' => true,
            'details' => [
                'test_alert' => true,
                'created_by' => 'admin',
            ],
        ]);

        $this->createAlertsForDisaster($disaster);

        return response()->json([
            'message' => 'Test alert sent successfully',
            'disaster' => $disaster,
        ]);
    }

    
    public function stopTestAlerts()
    {

        $testDisasters = Disaster::where('source', 'TEST_ALERT')->get();
        $count = $testDisasters->count();

        Disaster::where('source', 'TEST_ALERT')->delete();

        return response()->json([
            'message' => "Removed {$count} test alert(s) and their associated disasters successfully!",
        ]);
    }

    
    private function createAlertsForDisaster(Disaster $disaster)
    {
        $users = User::all();

        foreach ($users as $user) {

            $preference = UserPreference::where('user_id', $user->id)->first();

            $userLat = $preference->latitude ?? 14.5995;
            $userLon = $preference->longitude ?? 120.9842;
            $radiusKm = $preference->radius_km ?? 100;

            $distance = $this->calculateDistance(
                $userLat,
                $userLon,
                $disaster->latitude,
                $disaster->longitude
            );

            if ($distance <= $radiusKm) {
                Alert::create([
                    'disaster_id' => $disaster->id,
                    'user_id' => $user->id,
                    'severity' => $disaster->severity,
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        }
    }

    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
