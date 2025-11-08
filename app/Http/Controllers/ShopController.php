<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $teams = $user->teamMemberships()
            ->with(['team'])
            ->get()
            ->map(function ($membership) use ($user) {
                $points = UserPoint::where('team_id', $membership->team_id)
                    ->where('user_id', $user->id)
                    ->selectRaw('SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) - SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_points')
                    ->first();

                $membership->team->user_points = $points->total_points ?? 0;
                return $membership->team;
            });

        $shopItems = [
            [
                'id' => 1,
                'name' => 'Custom Badge',
                'description' => 'Create a custom badge for your team',
                'points_cost' => 100,
                'type' => 'badge',
                'available_for' => 'team_owner',
            ],
            [
                'id' => 2,
                'name' => 'Team Theme',
                'description' => 'Customize your team\'s appearance',
                'points_cost' => 50,
                'type' => 'theme',
                'available_for' => 'team_owner',
            ],
            [
                'id' => 3,
                'name' => 'Bonus Points Multiplier',
                'description' => '2x points for 24 hours',
                'points_cost' => 200,
                'type' => 'multiplier',
                'available_for' => 'all',
            ],
            [
                'id' => 4,
                'name' => 'Activity Boost',
                'description' => 'Create an extra activity this week',
                'points_cost' => 75,
                'type' => 'boost',
                'available_for' => 'team_owner',
            ],
        ];

        return Inertia::render('Shop/Index', [
            'teams' => $teams,
            'shopItems' => $shopItems,
        ]);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = $request->user();
        $itemId = $request->item_id;
        $teamId = $request->team_id;

        $teamMembership = $user->teamMemberships()
            ->where('team_id', $teamId)
            ->first();

        if (!$teamMembership) {
            return back()->with('error', 'You are not a member of this team.');
        }

        $userPoints = UserPoint::where('team_id', $teamId)
            ->where('user_id', $user->id)
            ->selectRaw('SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) - SUM(CASE WHEN type = "spent" THEN points ELSE 0 END) as total_points')
            ->first();

        $availablePoints = $userPoints->total_points ?? 0;

        $shopItems = [
            1 => ['name' => 'Custom Badge', 'points_cost' => 100, 'type' => 'badge', 'available_for' => 'team_owner'],
            2 => ['name' => 'Team Theme', 'points_cost' => 50, 'type' => 'theme', 'available_for' => 'team_owner'],
            3 => ['name' => 'Bonus Points Multiplier', 'points_cost' => 200, 'type' => 'multiplier', 'available_for' => 'all'],
            4 => ['name' => 'Activity Boost', 'points_cost' => 75, 'type' => 'boost', 'available_for' => 'team_owner'],
        ];

        if (!isset($shopItems[$itemId])) {
            return back()->with('error', 'Invalid item.');
        }

        $item = $shopItems[$itemId];

        if ($availablePoints < $item['points_cost']) {
            return back()->with('error', 'Insufficient points to purchase this item.');
        }

        if ($item['available_for'] === 'team_owner' && $teamMembership->role !== 'owner') {
            return back()->with('error', 'Only team owners can purchase this item.');
        }

        UserPoint::create([
            'user_id' => $user->id,
            'team_id' => $teamId,
            'points' => $item['points_cost'],
            'type' => 'spent',
            'description' => "Purchased: {$item['name']}",
        ]);

        switch ($item['type']) {
            case 'badge':

                break;
            case 'theme':

                break;
            case 'multiplier':

                break;
            case 'boost':

                break;
        }

        return back()->with('success', "Successfully purchased {$item['name']}!");
    }
}
