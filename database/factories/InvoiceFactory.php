<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'amount' => fake()->randomFloat(2, 100, 1000),
      'paid' => fake()->boolean(),
      'project_id' => Project::factory(),
      'client_id' => Client::factory(),
      'organization_id' => Organization::factory(),
    ];
  }
}
