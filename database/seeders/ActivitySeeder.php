<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        $users = User::all();

        foreach ($teams as $team) {
            // Create activities for each team
            $activities = [
                [
                    'name' => 'Code Review',
                    'description' => 'Review pull requests and provide constructive feedback',
                    'type' => 'individual',
                    'frequency' => 'daily',
                    'point_value' => 5,
                    'criteria' => ['min_comments' => 2, 'quality_threshold' => 'helpful'],
                ],
                [
                    'name' => 'Bug Fix',
                    'description' => 'Identify and fix bugs in the codebase',
                    'type' => 'individual',
                    'frequency' => 'one_time',
                    'point_value' => 15,
                    'criteria' => ['severity' => 'medium', 'verification_required' => true],
                ],
                [
                    'name' => 'Documentation Update',
                    'description' => 'Update or create documentation for features',
                    'type' => 'individual',
                    'frequency' => 'weekly',
                    'point_value' => 10,
                    'criteria' => ['min_words' => 100, 'includes_examples' => true],
                ],
                [
                    'name' => 'Team Meeting Attendance',
                    'description' => 'Attend and actively participate in team meetings',
                    'type' => 'team',
                    'frequency' => 'weekly',
                    'point_value' => 3,
                    'criteria' => ['participation_required' => true],
                ],
                [
                    'name' => 'Feature Implementation',
                    'description' => 'Complete a new feature from start to finish',
                    'type' => 'individual',
                    'frequency' => 'one_time',
                    'point_value' => 25,
                    'criteria' => ['code_review_passed' => true, 'tests_passed' => true],
                ],
            ];

            foreach ($activities as $activityData) {
                $activity = Activity::create([
                    'team_id' => $team->id,
                    ...$activityData,
                    'is_active' => true,
                    'start_date' => now(),
                    'end_date' => now()->addMonths(6),
                ]);

                // Create some sample tasks for each activity
                $taskCount = rand(3, 8);
                for ($i = 0; $i < $taskCount; $i++) {
                    $user = $users->random();
                    $status = ['pending', 'in_progress', 'completed', 'verified'][rand(0, 3)];

                    Task::create([
                        'activity_id' => $activity->id,
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'title' => "Task " . ($i + 1) . " for " . $activity->name,
                        'description' => "Sample task description for " . $activity->name,
                        'status' => $status,
                        'points_earned' => $status === 'completed' ? $activity->point_value : 0,
                        'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 30)) : null,
                        'verified_by' => $status === 'verified' ? $users->random()->id : null,
                        'verified_at' => $status === 'verified' ? now()->subDays(rand(1, 7)) : null,
                    ]);
                }
            }
        }
    }
}
