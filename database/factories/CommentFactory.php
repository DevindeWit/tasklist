<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => collect([
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
                ->implode("\n\n"),
        ];
    }
}
