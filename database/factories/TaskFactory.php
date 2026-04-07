<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => "### " . fake()->sentence() . "\n\n" . fake()->paragraphs(2, true) . "\n\n- " . fake()->word() . "\n- " . fake()->word(),
            'status' => fake()->randomElement(['todo', 'doing', 'done', 'blocked']),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
            'due_date' => fake()->optional(0.8)->dateTimeBetween('-1 month', '+2 months'),
            'estimate_minutes' => fake()->numberBetween(30, 480),
        ];
    }
}
