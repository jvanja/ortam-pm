<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class TimesheetFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'task_performed' => fake()->words(3, true),
      'worked_duration' => fake()->randomNumber(2),
      'project_id' => Project::inRandomOrder()->first(),
      'user_id' => User::inRandomOrder()->first(),
      'organization_id' => Organization::inRandomOrder()->first(),
    ];
  }
}
