<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $teams = $user->teamMemberships()
            ->with(['team.owner', 'team.members.user'])
            ->get()
            ->pluck('team');

        $achievements = $user->achievements()
            ->with(['badge', 'team'])
            ->latest('earned_at')
            ->take(5)
            ->get();

        $pointsSummary = $user->points()
            ->selectRaw('team_id, SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) as total_earned, SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_spent')
            ->groupBy('team_id')
            ->with('team')
            ->get()
            ->map(function ($summary) {
                $summary->net_points = $summary->total_earned - $summary->total_spent;
                return $summary;
            });

        $recentTasks = $user->tasks()
            ->with(['activity', 'team'])
            ->latest()
            ->take(10)
            ->get();

        $leaderboards = [];
        foreach ($teams as $team) {
            $leaderboards[$team->id] = UserPoint::where('team_id', $team->id)
                ->selectRaw('user_id, SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) - SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_points')
                ->groupBy('user_id')
                ->with('user')
                ->orderByDesc('total_points')
                ->take(10)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'teams' => $teams,
            'achievements' => $achievements,
            'pointsSummary' => $pointsSummary,
            'recentTasks' => $recentTasks,
            'leaderboards' => $leaderboards,
        ]);
    }
}
