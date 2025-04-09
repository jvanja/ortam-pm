<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
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
      'invoice_number' => 'inv-' . fake()->randomNumber(8),
      'amount' => fake()->randomFloat(2, 100, 1000),
      'status' => fake()->boolean(),
      'status' => fake()->randomElement(InvoiceStatus::cases()),
      'project_id' => Project::factory(),
      'client_id' => Client::factory(),
      'organization_id' => Organization::factory(),
    ];
  }
}
