<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $ownedTeams = $user->ownedTeams()->with(['members.user'])->get();
        $memberTeams = $user->teamMemberships()
            ->with(['team.owner', 'team.members.user'])
            ->get()
            ->pluck('team');

        return Inertia::render('Teams/Index', [
            'ownedTeams' => $ownedTeams,
            'memberTeams' => $memberTeams,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Teams/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $request->user()->id,
            'invite_code' => Str::random(8),
            'settings' => [
                'points_per_task' => 10,
                'bonus_points' => 5,
                'team_challenges_enabled' => true,
            ],
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
            'role' => 'owner',
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function show(Team $team): Response
    {
        $team->load(['owner', 'members.user', 'activities', 'badges', 'challenges']);

        $memberCount = $team->members()->count();
        $activeActivities = $team->activities()->where('is_active', true)->count();
        $totalBadges = $team->badges()->count();
        $activeChallenges = $team->challenges()->where('status', 'active')->count();

        $leaderboard = \App\Models\UserPoint::where('team_id', $team->id)
            ->selectRaw('user_id, SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) - SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_points')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_points')
            ->take(10)
            ->get();

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'memberCount' => $memberCount,
            'activeActivities' => $activeActivities,
            'totalBadges' => $totalBadges,
            'activeChallenges' => $activeChallenges,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:teams,invite_code',
        ]);

        $team = Team::where('invite_code', $request->invite_code)->first();

        if (!$team) {
            return back()->withErrors(['invite_code' => 'Invalid invite code.']);
        }

        $existingMember = TeamMember::where('team_id', $team->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingMember) {
            return back()->withErrors(['invite_code' => 'You are already a member of this team.']);
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
            'role' => 'member',
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Successfully joined the team!');
    }

    public function leave(Team $team, Request $request)
    {
        $member = TeamMember::where('team_id', $team->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$member) {
            return back()->withErrors(['error' => 'You are not a member of this team.']);
        }

        if ($member->role === 'owner') {
            return back()->withErrors(['error' => 'Team owners cannot leave their team.']);
        }

        $member->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Successfully left the team.');
    }
}
