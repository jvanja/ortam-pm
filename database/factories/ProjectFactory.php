<?php

namespace Database\Factories;

use App\Enums\ProjectType;
use App\Models\Client;
use App\Models\Organization;
use App\Enums\Language;
use App\Enums\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'type' => fake()->randomElement(ProjectType::cases()),
      'department' => fake()->word(),
      'manager_id' => User::inRandomOrder()->first(),
      'language' => fake()->randomElement(Language::cases()),
      'address' => fake()->address(),
      'budget' => fake()->randomFloat(2, 1000, 10000),
      'currency' => 'USD',
      'sales_representative_name' => fake()->name(),
      'status' => fake()->randomElement(ProjectStatus::cases()),
      'opening_date' => fake()->date(),
      'deadline' => fake()->dateTimeBetween('now', '+1 year'),
      'client_id' => Client::inRandomOrder()->first(),
      'organization_id' => Organization::inRandomOrder()->first(),
      'current_project_pipeline_stage_id' => null,
      'notes' => fake()->paragraph(),
    ];
  }
}
