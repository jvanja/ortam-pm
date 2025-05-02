<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Organization;
use App\Enums\Language;
use App\Enums\ProjectStatus;
use App\Models\ProjectPipelineStage;
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
      'type' => fake()->sentence(5),
      'department' => fake()->word(),
      'manager' => fake()->name(),
      'language' => fake()->randomElement(Language::cases()),
      'address' => fake()->address(),
      'budget' => fake()->randomFloat(2, 1000, 10000),
      'currency' => fake()->currencyCode(),
      'sales_representative_name' => fake()->name(),
      'status' => fake()->randomElement(ProjectStatus::cases()),
      'opening_date' => fake()->date(),
      'deadline' => fake()->dateTimeBetween('now', '+1 year'),
      'client_id' => Client::factory(),
      'organization_id' => Organization::factory(),
      'current_project_pipeline_stage_id' => ProjectPipelineStage::inRandomOrder()->first(),
    ];
  }
}
