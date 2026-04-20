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
            /**
             * Establish the relationship.
             * This will either use an existing factory or a passed ID.
             */
            'team_id' => \App\Models\Team::factory(),

            'name' => fake()->words(fake()->randomElement([2, 3]), true),

            /**
             * Use a closure to access the assigned team_id.
             * The $attributes array contains the evaluated values of the keys above.
             */
            'code' => function (array $attributes) {
                $teamId = $attributes['team_id'];
                $suffix = strtoupper(fake()->bothify('???-###'));

                return "$teamId-$suffix";
            },

            'description' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['active', 'on_hold', 'archived']),
        ];
    }
}
