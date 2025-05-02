<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PipelineStage>
 */
class PipelineStageFactory extends Factory {
  protected $model = PipelineStage::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'default_name' => fake()->words(2, true),
      'is_system_default' => fake()->boolean(75), // Mostly true for testing defaults
      'default_order' => fake()->numberBetween(1, 10) * 10,
    ];
  }

  /**
   * Indicate that the stage is a system default.
   */
  public function systemDefault(): static {
    return $this->state(fn(array $attributes) => [
      'is_system_default' => true,
    ]);
  }
}
