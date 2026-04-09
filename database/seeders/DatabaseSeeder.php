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
    public function run(): void
    {
        // 1. Seed Global Tags first
        $tags = Tag::factory(12)->create();

        // 2. Seed Teams using a loop
        Team::factory(3)->create()->each(function ($team) use ($tags) {

            // Create the Manager and assign them as the Team Owner
            $manager = User::factory()->create([
                'role' => 'manager',
                'team_id' => $team->id,
            ]);
            $team->update(['owner_id' => $manager->id]);

            // Create Members for this team
            $members = User::factory(4)->create([
                'team_id' => $team->id,
                'role' => 'member',
            ]);

            $allTeamUsers = $members->push($manager);

            // 3. Create Projects for the team
            Project::factory(rand(2, 4))->create([
                'team_id' => $team->id
            ])->each(function ($project) use ($allTeamUsers, $tags) {

                // 4. Create Tasks for each project
                Task::factory(rand(4, 7))->create([
                    'project_id' => $project->id,
                    'assignee_id' => $allTeamUsers->random()->id,
                ])->each(function ($task) use ($allTeamUsers, $tags) {

                    // 5. Seed Pivot Data: Tagging
                    // We loop so each tag can have a DIFFERENT 'added_by' user
                    $randomTags = $tags->random(rand(1, 3));
                    foreach ($randomTags as $tag) {
                        $task->tags()->attach($tag->id, [
                            'added_by' => $allTeamUsers->random()->id,
                            'created_at' => now(),
                        ]);
                    }

                    // 6. Seed Pivot Data: Watchers
                    $task->watchers()->attach(
                        $allTeamUsers->random(rand(1, 2))->pluck('id'),
                        ['notify_email' => fake()->boolean()]
                    );

                    // 7. Seed Comments
                    Comment::factory(rand(1, 3))->create([
                        'task_id' => $task->id,
                        'user_id' => $allTeamUsers->random()->id
                    ]);
                });

                // 8. ADD VARIETY: Seed a couple of Soft-Deleted tasks
                Task::factory(2)->trashed()->create([
                    'project_id' => $project->id,
                    'assignee_id' => $allTeamUsers->random()->id,
                ]);
            });
        });

        // 9. Global Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'team_id' => null,
        ]);
    }
}
