<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Global admin account
        User::factory()->admin()->create([
            'name'  => 'Admin',
            'email' => 'admin@test.com',
        ]);

        // 2 randomised teams, targeting 40–60 tasks total
        $tasksPerTeam = intdiv(rand(40, 60), 2);

        Team::factory(2)->create()->each(
            fn(Team $team) => $this->seedTeam($team, $tasksPerTeam)
        );

        // Test team with predictable credentials (documented in README)
        $testTeam = Team::factory()->create(['name' => 'Test Team']);

        $testManager = User::factory()->manager()->create([
            'name'    => 'Test Manager',
            'email'   => 'manager@test.com',
            'team_id' => $testTeam->id,
        ]);

        $testTeam->update(['owner_id' => $testManager->id]);

        User::factory()->create([
            'name'    => 'Test Member',
            'email'   => 'member@test.com',
            'team_id' => $testTeam->id,
        ]);

        $this->seedTeam($testTeam, $tasksPerTeam);
    }

    private function seedTeam(Team $team, int $taskCount): void
    {
        $manager = User::factory()->manager()->create(['team_id' => $team->id]);
        $team->update(['owner_id' => $manager->id]);

        $members  = User::factory(3)->create(['team_id' => $team->id]);
        $allUsers = $members->push($manager);

        $projects = Project::factory(rand(3, 5))->create(['team_id' => $team->id]);

        // Distribute task count roughly evenly across projects
        $tasksPerProject = intdiv($taskCount, $projects->count());

        foreach ($projects as $project) {
            $tagNames = collect([
                'Bug',
                'Feature',
                'Urgent',
                'Design',
                'Backend',
                'Frontend',
                'Research',
                'Review',
                'Testing',
                'Blocked',
                'Documentation',
                'Performance',
                'Security',
                'Refactor',
                'Discussion',
            ])->shuffle()->take(rand(6, 10));

            $tags = $tagNames->map(fn($name) => Tag::factory()->create([
                'project_id' => $project->id,
                'name'       => $name,
            ]))->values();

            for ($i = 0; $i < $tasksPerProject; $i++) {
                $task = Task::factory()->create([
                    'project_id'  => $project->id,
                    'assignee_id' => $allUsers->random()->id,
                ]);

                $this->attachTags($task, $tags, $allUsers);
                $this->attachWatchers($task, $allUsers);
                $this->attachComments($task, $allUsers);
            }
        }
    }

    private function attachTags(Task $task, $tags, Collection $users): void
    {
        foreach ($tags->random(rand(1, 3)) as $tag) {
            $task->tags()->attach($tag->id, [
                'added_by' => $users->random()->id,
            ]);
        }
    }

    private function attachWatchers(Task $task, Collection $users): void
    {
        $task->watchers()->attach(
            $users->random(rand(1, 2))->pluck('id'),
            ['notify_email' => fake()->boolean()]
        );
    }

    private function attachComments(Task $task, Collection $users): void
    {
        Comment::factory(rand(0, 3))->create([
            'task_id' => $task->id,
            'user_id' => $users->random()->id,
        ]);
    }
}
