<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function show(Request $request)
    {
        $preference = UserPreference::where('user_id', $request->user()?->id)
            ->first();

        return response()->json($preference ?? [
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'location_name' => 'Manila, Philippines',
            'country' => 'Philippines',
            'radius_km' => 100,
            'min_magnitude' => 3.0,
            'email_alerts' => true,
            'push_alerts' => true,
            'sound_alerts' => true,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'required|string|max:255',
            'radius_km' => 'required|integer|min:1|max:1000',
            'min_magnitude' => 'required|numeric|min:0|max:10',
            'email_alerts' => 'boolean',
            'push_alerts' => 'boolean',
            'sound_alerts' => 'boolean',
            'alert_types' => 'array',
        ]);

        $preference = UserPreference::updateOrCreate(
            ['user_id' => $request->user()?->id],
            $validated
        );

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preference' => $preference,
        ]);
    }
}
