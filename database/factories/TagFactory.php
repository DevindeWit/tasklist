<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hex_color'   => fake()->hexColor(),
            'description' => fake()->optional()->sentence(),
            'project_id'     => Project::factory(),
        ];
    }
}
