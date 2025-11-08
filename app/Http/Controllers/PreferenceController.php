<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function show(Request $request)
    {
        $sessionId = $request->session()->getId();

        $preference = UserPreference::where('session_id', $sessionId)
            ->orWhere('user_id', $request->user()?->id)
            ->first();

        if (!$preference) {
            $preference = UserPreference::create([
                'session_id' => $sessionId,
                'user_id' => $request->user()?->id,
            ]);
        }

        return response()->json($preference);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'min_magnitude' => 'nullable|numeric|min:0|max:10',
            'radius_km' => 'nullable|integer|min:1|max:20000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'email_alerts' => 'nullable|boolean',
            'push_alerts' => 'nullable|boolean',
            'sound_alerts' => 'nullable|boolean',
        ]);

        $sessionId = $request->session()->getId();

        $preference = UserPreference::where('session_id', $sessionId)
            ->orWhere('user_id', $request->user()?->id)
            ->first();

        if ($preference) {
            $preference->update($validated);
        } else {
            $preference = UserPreference::create(array_merge($validated, [
                'session_id' => $sessionId,
                'user_id' => $request->user()?->id,
            ]));
        }

        return response()->json($preference);
    }
}
