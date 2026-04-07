<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Comment;
use App\Models\Team;
use App\Models\Tag;
use App\Models\Project;
use App\Models\Task;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // 1. Create exactly 8 Tags (Requirement: 6-10)
    $tags = Tag::factory(8)->create();

    // 2. Loop twice to create exactly 2 Teams
    for ($i = 0; $i < 2; $i++) {

        // A. Create the Manager (who will own the team)
        $manager = User::factory()->create([
            'role' => 'manager',
            'email' => "manager{$i}@example.com" // Easy login for testing
        ]);

        // B. Create the Team and assign the Manager as the owner
        $team = Team::factory()->create([
            'owner_id' => $manager->id
        ]);

        // C. Attach the Manager to the team's pivot table so they are an actual member
        $team->members()->attach($manager->id);

        // D. Create exactly 3 Members and attach them to the team
        $members = User::factory(3)->create(['role' => 'member']);
        $team->members()->attach($members->pluck('id'));

        // All users in this specific team (Manager + 3 Members)
        $teamUsers = $team->members;

        // E. Create 4 Projects for this team (Requirement: 3-5)
        $projects = Project::factory(4)->create([
            'team_id' => $team->id
        ]);

        foreach ($projects as $project) {
            // F. Create 6 Tasks per project (4 projects * 6 tasks * 2 teams = 48 total tasks. Fits requirement: 40-60)
            $tasks = Task::factory(6)->create([
                'project_id' => $project->id,
                'assignee_id' => $teamUsers->random()->id // Must be a team member
            ]);

            foreach ($tasks as $task) {
                // G. Attach 1-3 random tags, recording who added them
                $task->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id'),
                    ['added_by' => $teamUsers->random()->id]
                );

                // H. Attach 1-2 random watchers with email notification boolean
                $task->watchers()->attach(
                    $teamUsers->random(rand(1, 2))->pluck('id'),
                    ['notify_email' => fake()->boolean()]
                );

                // I. Create 0-3 comments per task
                Comment::factory(rand(0, 3))->create([
                    'task_id' => $task->id,
                    'user_id' => $teamUsers->random()->id // Comment must be from a team member
                ]);
            }
        }
    }

    // Optional: Add a master Admin for yourself to test everything globally
    User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'admin@test.com',
        'role' => 'admin',
    ]);
}
}
