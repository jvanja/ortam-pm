<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Task;

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
      'task_performed' => fake()->randomElement(Task::cases()),
      'worked_duration' => fake()->randomNumber(2),
      'project_id' => Project::inRandomOrder()->first(),
      'user_id' => User::inRandomOrder()->first(),
      'organization_id' => Organization::inRandomOrder()->first(),
      // 'project_id' => Project::factory(),
      // 'user_id' => User::factory(),
      // 'organization_id' => Organization::factory(),
    ];
  }
}
