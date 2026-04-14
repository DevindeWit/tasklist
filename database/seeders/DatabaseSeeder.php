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
        // Global Admin
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
        ]);

        // 1. Create 6-10 Global Tags
        $tags = Tag::factory(rand(6, 10))->create();

        // 2. Prepare Task Distribution (Targeting 40-60 total)
        $totalTasksTarget = rand(40, 60);
        $tasksPerTeam = intdiv($totalTasksTarget, 2);

        // 3. Create exactly 2 Teams
        Team::factory(2)->create()->each(function ($team) use ($tags, $tasksPerTeam) {

            // A. Create 1 Manager and set as Team Owner
            $manager = User::factory()->manager()->create(['team_id' => $team->id]);
            $team->update(['owner_id' => $manager->id]);

            // B. Create 3 Members
            $members = User::factory(3)->create(['team_id' => $team->id]);
            $allUsers = $members->push($manager);

            // C. Create 3-5 Projects per team
            $projects = Project::factory(rand(3, 5))->create(['team_id' => $team->id]);

            // D. Create the Tasks for this team
            for ($i = 0; $i < $tasksPerTeam; $i++) {
                $task = Task::factory()->create([
                    'project_id' => $projects->random()->id,
                    'assignee_id' => $allUsers->random()->id,
                ]);

                // E. Randomized Taggings (Pivot: added_by)
                $taskTags = $tags->random(rand(1, 3));
                foreach ($taskTags as $tag) {
                    $task->tags()->attach($tag->id, [
                        'added_by' => $allUsers->random()->id
                    ]);
                }

                // F. Randomized Watchers (Pivot: notify_email)
                $task->watchers()->attach(
                    $allUsers->random(rand(1, 2))->pluck('id'),
                    ['notify_email' => fake()->boolean()]
                );

                // G. 0-3 Comments per task
                Comment::factory(rand(0, 3))->create([
                    'task_id' => $task->id,
                    'user_id' => $allUsers->random()->id
                ]);
            }
        });

        // Test team with test users with login information in README.md
        $testTeam = Team::factory()->create(['name' => 'Test Team']);

        $manager = User::factory()->manager()->create([
            'name' => 'Test Manager',
            'email' => 'manager@test.com',
            'role' => 'manager',
            'team_id' => $testTeam->id,
        ]);

        $testTeam->update([
            'owner_id' => $manager->id,
        ]);

        User::factory()->create([
            'name' => 'Test Member',
            'email' => 'member@test.com',
            'team_id' => $testTeam->id,
        ]);
    }
}
