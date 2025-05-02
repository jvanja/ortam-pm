<?php

namespace Database\Factories;

use App\Models\ProjectPipelineHistory;
use App\Models\ProjectPipelineStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectPipelineHistory>
 */
class ProjectPipelineHistoryFactory extends Factory {
  protected $model = ProjectPipelineHistory::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    $stage = ProjectPipelineStage::factory()->create(); // Ensure a stage exists

    return [
      'project_id' => $stage->project_id,
      'project_pipeline_stage_id' => $stage->id,
      'action' => fake()->randomElement(['started', 'completed', 'status_changed', 'stage_added', 'stage_removed']),
      'details' => fake()->optional()->sentence,
      'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id, // Get existing or create new user
      'changed_at' => fake()->dateTimeThisYear(),
    ];
  }
}
