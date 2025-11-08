<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamChallenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            // Create challenges for each team
            $challenges = [
                [
                    'title' => 'Sprint Completion Challenge',
                    'description' => 'Complete all tasks assigned in the current sprint',
                    'type' => 'collaborative',
                    'target_points' => 500,
                    'reward_points' => 100,
                    'requirements' => [
                        'task_completion_rate' => 100,
                        'team_participation' => 80,
                    ],
                    'start_date' => now()->subDays(7),
                    'end_date' => now()->addDays(14),
                    'status' => 'active',
                    'progress' => rand(20, 80),
                ],
                [
                    'title' => 'Bug Hunt Challenge',
                    'description' => 'Find and fix the most bugs in a week',
                    'type' => 'competitive',
                    'target_points' => 200,
                    'reward_points' => 50,
                    'requirements' => [
                        'bug_fixes' => 10,
                        'quality_score' => 8,
                    ],
                    'start_date' => now()->subDays(3),
                    'end_date' => now()->addDays(4),
                    'status' => 'active',
                    'progress' => rand(30, 90),
                ],
                [
                    'title' => 'Documentation Sprint',
                    'description' => 'Update all project documentation',
                    'type' => 'collaborative',
                    'target_points' => 300,
                    'reward_points' => 75,
                    'requirements' => [
                        'docs_updated' => 5,
                        'review_completed' => true,
                    ],
                    'start_date' => now()->subDays(10),
                    'end_date' => now()->addDays(7),
                    'status' => 'active',
                    'progress' => rand(40, 95),
                ],
                [
                    'title' => 'Code Review Marathon',
                    'description' => 'Complete 50 code reviews as a team',
                    'type' => 'collaborative',
                    'target_points' => 400,
                    'reward_points' => 80,
                    'requirements' => [
                        'total_reviews' => 50,
                        'quality_threshold' => 7,
                    ],
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->addDays(10),
                    'status' => 'active',
                    'progress' => rand(25, 85),
                ],
                [
                    'title' => 'Feature Release Race',
                    'description' => 'Release a new feature before the deadline',
                    'type' => 'milestone',
                    'target_points' => 600,
                    'reward_points' => 150,
                    'requirements' => [
                        'feature_complete' => true,
                        'tests_passed' => true,
                        'deployment_successful' => true,
                    ],
                    'start_date' => now()->subDays(14),
                    'end_date' => now()->addDays(21),
                    'status' => 'active',
                    'progress' => rand(15, 70),
                ],
            ];

            foreach ($challenges as $challengeData) {
                TeamChallenge::create([
                    'team_id' => $team->id,
                    ...$challengeData,
                ]);
            }
        }
    }
}
