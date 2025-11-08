<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample users if they don't exist
        $users = User::factory(5)->create();

        // Create sample teams
        $teams = [
            [
                'name' => 'Development Team',
                'description' => 'Our main software development team working on the flagship product.',
                'owner_id' => $users[0]->id,
                'invite_code' => Str::random(8),
                'settings' => [
                    'points_per_task' => 10,
                    'bonus_points' => 5,
                    'team_challenges_enabled' => true,
                ],
            ],
            [
                'name' => 'Design Team',
                'description' => 'Creative team responsible for UI/UX design and branding.',
                'owner_id' => $users[1]->id,
                'invite_code' => Str::random(8),
                'settings' => [
                    'points_per_task' => 8,
                    'bonus_points' => 3,
                    'team_challenges_enabled' => true,
                ],
            ],
            [
                'name' => 'Marketing Team',
                'description' => 'Team focused on digital marketing and customer acquisition.',
                'owner_id' => $users[2]->id,
                'invite_code' => Str::random(8),
                'settings' => [
                    'points_per_task' => 12,
                    'bonus_points' => 7,
                    'team_challenges_enabled' => true,
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            $team = Team::create($teamData);

            // Add team members (including the owner)
            foreach ($users as $index => $user) {
                $role = $index === 0 ? 'owner' : ($index === 1 ? 'admin' : 'member');

                TeamMember::create([
                    'team_id' => $team->id,
                    'user_id' => $user->id,
                    'role' => $role,
                ]);
            }
        }
    }
}
