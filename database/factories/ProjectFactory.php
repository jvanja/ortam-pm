<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
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
            'project_type' => fake()->jobTitle(),
            'department' => fake()->word(),
            'project_manager' => fake()->name(),
            'project_language' => fake()->randomElement(Language::cases()),
            'project_address' => fake()->address(),
            'budget' => fake()->randomFloat(2, 1000, 10000),
            'sales_representative_name' => fake()->name(),
            'project_status' => fake()->randomElement(ProjectStatus::cases()),
            'project_opening_date' => fake()->date(),
            'deadline' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}

