<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Team;
use App\Models\UserAchievement;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GamificationController extends Controller
{
    public function leaderboard(Team $team): Response
    {
        $leaderboard = UserPoint::where('team_id', $team->id)
            ->selectRaw('user_id, SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) - SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_points')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_points')
            ->get();

        return Inertia::render('Gamification/Leaderboard', [
            'team' => $team,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function badges(Request $request, Team $team): Response
    {
        $badges = $team->badges()
            ->with(['userAchievements.user'])
            ->get();

        $userAchievements = UserAchievement::where('team_id', $team->id)
            ->where('user_id', $request->user()->id)
            ->with(['badge'])
            ->get();

        return Inertia::render('Gamification/Badges', [
            'team' => $team,
            'badges' => $badges,
            'userAchievements' => $userAchievements,
        ]);
    }

    public function points(Request $request, Team $team): Response
    {
        $user = $request->user();

        $pointsHistory = $user->points()
            ->where('team_id', $team->id)
            ->with(['user'])
            ->latest()
            ->paginate(20);

        $pointsSummary = $user->points()
            ->where('team_id', $team->id)
            ->selectRaw('SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) as total_earned, SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_spent')
            ->first();

        $netPoints = ($pointsSummary->total_earned ?? 0) - ($pointsSummary->total_spent ?? 0);

        return Inertia::render('Gamification/Points', [
            'team' => $team,
            'pointsHistory' => $pointsHistory,
            'pointsSummary' => [
                'total_earned' => $pointsSummary->total_earned ?? 0,
                'total_spent' => $pointsSummary->total_spent ?? 0,
                'net_points' => $netPoints,
            ],
        ]);
    }

    public function createBadge(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'required|string|max:7',
            'type' => 'required|in:achievement,milestone,special',
            'points_reward' => 'required|integer|min:0',
            'criteria' => 'nullable|array',
        ]);

        $badge = Badge::create([
            'team_id' => $team->id,
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'type' => $request->type,
            'points_reward' => $request->points_reward,
            'criteria' => $request->criteria ?? [],
            'is_active' => true,
        ]);

        return back()->with('success', 'Badge created successfully!');
    }

    public function awardBadge(Request $request, Team $team, Badge $badge)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user already has this badge
        $existingAchievement = UserAchievement::where('user_id', $request->user_id)
            ->where('badge_id', $badge->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existingAchievement) {
            return back()->withErrors(['error' => 'User already has this badge.']);
        }

        // Award the badge
        UserAchievement::create([
            'user_id' => $request->user_id,
            'badge_id' => $badge->id,
            'team_id' => $team->id,
            'earned_at' => now(),
            'metadata' => [
                'awarded_by' => $request->user()->id,
                'awarded_at' => now(),
            ],
        ]);

        // Award bonus points if any
        if ($badge->points_reward > 0) {
            UserPoint::create([
                'user_id' => $request->user_id,
                'team_id' => $team->id,
                'points' => $badge->points_reward,
                'type' => 'bonus',
                'reason' => "Badge reward: {$badge->name}",
                'source_id' => $badge->id,
                'source_type' => 'App\Models\Badge',
            ]);
        }

        return back()->with('success', 'Badge awarded successfully!');
    }

    public function addPoints(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer',
            'type' => 'required|in:earned,spent,bonus,penalty',
            'reason' => 'required|string|max:255',
        ]);

        UserPoint::create([
            'user_id' => $request->user_id,
            'team_id' => $team->id,
            'points' => $request->points,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Points added successfully!');
    }
}
