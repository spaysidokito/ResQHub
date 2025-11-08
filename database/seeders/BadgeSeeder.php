<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        $users = User::all();

        foreach ($teams as $team) {
            // Create badges for each team
            $badges = [
                [
                    'name' => 'First Task',
                    'description' => 'Complete your first task',
                    'icon' => 'star',
                    'color' => '#10B981',
                    'type' => 'achievement',
                    'criteria' => ['task_count' => 1],
                    'points_reward' => 10,
                ],
                [
                    'name' => 'Task Master',
                    'description' => 'Complete 10 tasks',
                    'icon' => 'trophy',
                    'color' => '#F59E0B',
                    'type' => 'achievement',
                    'criteria' => ['task_count' => 10],
                    'points_reward' => 50,
                ],
                [
                    'name' => 'Team Player',
                    'description' => 'Participate in 5 team activities',
                    'icon' => 'users',
                    'color' => '#3B82F6',
                    'type' => 'achievement',
                    'criteria' => ['team_activities' => 5],
                    'points_reward' => 25,
                ],
                [
                    'name' => 'Bug Hunter',
                    'description' => 'Fix 5 bugs',
                    'icon' => 'bug',
                    'color' => '#EF4444',
                    'type' => 'achievement',
                    'criteria' => ['bug_fixes' => 5],
                    'points_reward' => 75,
                ],
                [
                    'name' => 'Documentation Guru',
                    'description' => 'Update documentation 3 times',
                    'icon' => 'book-open',
                    'color' => '#8B5CF6',
                    'type' => 'achievement',
                    'criteria' => ['docs_updates' => 3],
                    'points_reward' => 30,
                ],
                [
                    'name' => 'Code Reviewer',
                    'description' => 'Review 10 pull requests',
                    'icon' => 'eye',
                    'color' => '#06B6D4',
                    'type' => 'achievement',
                    'criteria' => ['code_reviews' => 10],
                    'points_reward' => 40,
                ],
                [
                    'name' => 'Sprint Champion',
                    'description' => 'Complete all tasks in a sprint',
                    'icon' => 'award',
                    'color' => '#EC4899',
                    'type' => 'milestone',
                    'criteria' => ['sprint_completion' => 100],
                    'points_reward' => 100,
                ],
                [
                    'name' => 'Collaboration Champion',
                    'description' => 'Help 3 different team members',
                    'icon' => 'heart',
                    'color' => '#F97316',
                    'type' => 'special',
                    'criteria' => ['help_others' => 3],
                    'points_reward' => 60,
                ],
            ];

            foreach ($badges as $badgeData) {
                $badge = Badge::create([
                    'team_id' => $team->id,
                    ...$badgeData,
                    'is_active' => true,
                ]);

                // Randomly award some badges to users
                foreach ($users as $user) {
                    if (rand(1, 3) === 1) { // 33% chance to award badge
                        UserAchievement::create([
                            'user_id' => $user->id,
                            'badge_id' => $badge->id,
                            'team_id' => $team->id,
                            'earned_at' => now()->subDays(rand(1, 60)),
                            'metadata' => [
                                'earned_through' => 'sample_data',
                                'task_count' => rand(1, 15),
                            ],
                        ]);
                    }
                }
            }
        }
    }
}
