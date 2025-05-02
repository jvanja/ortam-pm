<?php

namespace Database\Factories;

use App\Models\PipelineStage;
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
    // Attempt to get a default stage or create one
    $pipelineStage = PipelineStage::inRandomOrder()->first() ?? PipelineStage::factory()->create();

    return [
      'project_id' => Project::factory(),
      'pipeline_stage_id' => $pipelineStage->id,
      'name' => $pipelineStage->default_name, // Default to stage name
      'stage_order' => fake()->numberBetween(1, 10) * 10,
      'status' => 'pending',
      'completed_at' => null,
      'notes' => fake()->optional()->sentence,
    ];
  }

  /**
   * Indicate that the stage is completed.
   */
  public function completed(): static {
    return $this->state(fn(array $attributes) => [
      'status' => 'completed',
      'completed_at' => now(),
    ]);
  }

  /**
   * Indicate that the stage is pending.
   */
  public function pending(): static {
    return $this->state(fn(array $attributes) => [
      'status' => 'pending',
      'completed_at' => null,
    ]);
  }

  /**
   * Indicate that the stage is skipped.
   */
  public function skipped(): static {
    return $this->state(fn(array $attributes) => [
      'status' => 'skipped',
      'completed_at' => null, // Or now(), depending on logic
    ]);
  }

  /**
   * Use a custom name instead of the default stage name.
   */
  public function customName(string $name): static {
    return $this->state(fn(array $attributes) => [
      'name' => $name,
      'pipeline_stage_id' => null, // Often custom stages aren't linked to a default
    ]);
  }
}
