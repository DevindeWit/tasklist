<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(fake()->randomElement([2, 3]), true),
            'code' => strtoupper(fake()->bothify('???-###')),
            'description' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['active', 'on_hold', 'archived']),
        ];
    }
}
