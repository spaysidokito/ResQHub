<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\Team;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request, Team $team): Response
    {
        $activities = $team->activities()
            ->with(['tasks.user'])
            ->get();

        $userTasks = Task::where('team_id', $team->id)
            ->where('user_id', $request->user()->id)
            ->with(['activity'])
            ->latest()
            ->get();

        return Inertia::render('Activities/Index', [
            'team' => $team,
            'activities' => $activities,
            'userTasks' => $userTasks,
        ]);
    }

    public function create(Team $team): Response
    {
        return Inertia::render('Activities/Create', [
            'team' => $team,
        ]);
    }

    public function store(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:individual,team,competitive',
            'frequency' => 'required|in:one_time,daily,weekly,monthly',
            'point_value' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $activity = Activity::create([
            'team_id' => $team->id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'frequency' => $request->frequency,
            'point_value' => $request->point_value,
            'criteria' => $request->criteria ?? [],
            'is_active' => true,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('teams.activities.index', $team)
            ->with('success', 'Activity created successfully!');
    }

    public function show(Request $request, Team $team, Activity $activity): Response
    {
        $activity->load(['tasks.user', 'team']);

        $userTasks = $activity->tasks()
            ->where('user_id', $request->user()->id)
            ->get();

        return Inertia::render('Activities/Show', [
            'team' => $team,
            'activity' => $activity,
            'userTasks' => $userTasks,
        ]);
    }

    public function createTask(Request $request, Team $team, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create([
            'activity_id' => $activity->id,
            'user_id' => $request->user()->id,
            'team_id' => $team->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Task created successfully!');
    }

    public function updateTaskStatus(Request $request, Team $team, Activity $activity, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,verified',
        ]);

        $oldStatus = $task->status;
        $newStatus = $request->status;

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        // Award points when task is completed
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $points = $activity->point_value;

            UserPoint::create([
                'user_id' => $task->user_id,
                'team_id' => $team->id,
                'points' => $points,
                'type' => 'earned',
                'reason' => "Completed task: {$task->title}",
                'source_id' => $task->id,
                'source_type' => 'App\Models\Task',
            ]);

            $task->update(['points_earned' => $points]);
        }

        return back()->with('success', 'Task status updated successfully!');
    }

    public function verifyTask(Request $request, Team $team, Activity $activity, Task $task)
    {
        $request->validate([
            'verified' => 'required|boolean',
        ]);

        if ($request->verified) {
            $task->update([
                'status' => 'verified',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            return back()->with('success', 'Task verified successfully!');
        } else {
            $task->update([
                'status' => 'completed',
                'verified_by' => null,
                'verified_at' => null,
            ]);

            return back()->with('success', 'Task verification removed.');
        }
    }
}
