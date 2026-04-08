<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Comment;
use App\Models\Team;
use App\Models\Tag;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create exactly 8 Tags
        $tags = Tag::factory(8)->create();

        for ($i = 0; $i < 2; $i++) {

            // Create the Manager (role only per team, not global)
            $manager = User::factory()->create([
                'email' => "manager{$i}@example.com",
            ]);

            // Create the Team and assign the Manager as owner
            $team = Team::factory()->create([
                'owner_id' => $manager->id,
            ]);

            // Attach Manager WITH ROLE using pivot model
            $team->members()->syncWithoutDetaching([
                $manager->id => ['user_role' => 'manager']
            ]);

            // Create Members
            $members = User::factory(3)->create();

            // Attach Members WITH ROLE using pivot model
            $team->members()->syncWithoutDetaching(
                $members->pluck('id')->mapWithKeys(fn($id) => [
                    $id => ['user_role' => 'member']
                ])->toArray()
            );

            // All users in this team (fresh from pivot)
            $teamUsers = $team->members;

            // Create Projects
            $projects = Project::factory(random_int(3, 5))->create([
                'team_id' => $team->id
            ]);

            foreach ($projects as $project) {

                $tasks = Task::factory(random_int(4, 8))->create([
                    'project_id' => $project->id,
                    'assignee_id' => $teamUsers->random()->id
                ]);

                foreach ($tasks as $task) {

                    // Attach Tags
                    $task->tags()->attach(
                        $tags->random(rand(1, 3))->pluck('id'),
                        ['added_by' => $teamUsers->random()->id]
                    );

                    // Attach Watchers
                    $task->watchers()->attach(
                        $teamUsers->random(rand(1, 2))->pluck('id'),
                        ['notify_email' => fake()->boolean()]
                    );

                    // Create Comments
                    Comment::factory(rand(0, 3))->create([
                        'task_id' => $task->id,
                        'user_id' => $teamUsers->random()->id
                    ]);
                }
            }
        }

        // Optional global Admin (not tied to team roles)
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'is_super_user' => true
        ]);
    }
}
