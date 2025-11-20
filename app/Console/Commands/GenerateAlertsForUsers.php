<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Disaster;
use App\Models\Earthquake;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Console\Command;

class GenerateAlertsForUsers extends Command
{
    protected $signature = 'alerts:generate {--user-id= : Generate alerts for specific user}';
    protected $description = 'Generate alerts for users based on active disasters and earthquakes';

    public function handle(): int
    {
        $this->info('Generating alerts for users...');

        if ($this->option('user-id')) {
            $users = User::where('id', $this->option('user-id'))->get();
        } else {
            $users = User::all();
        }

        $totalAlerts = 0;

        foreach ($users as $user) {
            $count = $this->generateAlertsForUser($user);
            $totalAlerts += $count;
            $this->info("Generated {$count} alert(s) for user: {$user->name}");
        }

        $this->info("Total alerts generated: {$totalAlerts}");

        return Command::SUCCESS;
    }

    private function generateAlertsForUser(User $user): int
    {
        $preference = UserPreference::where('user_id', $user->id)->first();

        $userLat = $preference->latitude ?? 14.5995;
        $userLon = $preference->longitude ?? 120.9842;
        $radiusKm = $preference->radius_km ?? 100;
        $minMagnitude = $preference->min_magnitude ?? 3.0;

        $count = 0;

        $disasters = Disaster::where('status', 'active')->get();
        foreach ($disasters as $disaster) {
            $distance = $this->calculateDistance(
                $userLat,
                $userLon,
                $disaster->latitude,
                $disaster->longitude
            );

            if ($distance <= $radiusKm) {
                $existing = Alert::where('disaster_id', $disaster->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existing) {
                    Alert::create([
                        'disaster_id' => $disaster->id,
                        'user_id' => $user->id,
                        'severity' => $disaster->severity,
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                    $count++;
                }
            }
        }

        $earthquakes = Earthquake::where('occurred_at', '>=', now()->subDays(7))
            ->where('magnitude', '>=', $minMagnitude)
            ->get();

        foreach ($earthquakes as $earthquake) {
            $distance = $this->calculateDistance(
                $userLat,
                $userLon,
                $earthquake->latitude,
                $earthquake->longitude
            );

            if ($distance <= $radiusKm) {
                $existing = Alert::where('earthquake_id', $earthquake->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existing) {
                    Alert::create([
                        'earthquake_id' => $earthquake->id,
                        'user_id' => $user->id,
                        'severity' => $this->getEarthquakeSeverity($earthquake->magnitude),
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                    $count++;
                }
            }
        }

        return $count;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function getEarthquakeSeverity(float $magnitude): string
    {
        if ($magnitude >= 7.0) return 'critical';
        if ($magnitude >= 6.0) return 'high';
        if ($magnitude >= 4.5) return 'moderate';
        return 'low';
    }
}
