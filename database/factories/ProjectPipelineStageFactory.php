<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectPipelineStage>
 */
class ProjectPipelineStageFactory extends Factory {
  protected $model = ProjectPipelineStage::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'project_id' => Project::factory(),
      'name' => fake()->word(),
      'stage_order' => fake()->numberBetween(1, 10) * 10,
      'is_system_default' => false,
      'status' => 'pending',
      'completed_at' => null,
      'notes' => fake()->optional()->sentence,
    ];
  }
}
