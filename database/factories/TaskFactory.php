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
            'description' => fake()->optional(0.8)->passthrough(
                collect([
                    fn() => '# ' . fake()->sentence(),
                    fn() => '## ' . fake()->sentence(),
                    fn() => fake()->paragraph(),
                    fn() => fake()->sentence(),
                    fn() => '**' . fake()->words(3, true) . '** — ' . fake()->sentence(),
                    fn() => '- ' . implode("\n- ", fake()->words(fake()->numberBetween(3, 6))),
                    fn() => '> ' . fake()->sentence(),
                    fn() => '```php' . "\n" . 'echo "' . fake()->word() . '";' . "\n" . '```',
                    fn() => '[' . fake()->words(2, true) . '](' . fake()->url() . ')',
                ])
                    ->random(fake()->numberBetween(1, 8))
                    ->map(fn($fn) => $fn())
                    ->implode("\n\n")
            ),
            'status' => fake()->randomElement(['todo', 'doing', 'done', 'blocked']),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
            'due_date' => fake()->optional(0.8)->dateTimeBetween('-1 month', '+2 months'),
            'estimate_minutes' => fake()->numberBetween(30, 480),
        ];
    }
}
