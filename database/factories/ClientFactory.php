<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'company_name' => fake()->company(),
      'contact_person' => fake()->name(),
      'address' => fake()->address(),
      'phone' => fake()->phoneNumber(),
      'email' => fake()->email(),
      'organization_id' => Organization::factory(),
    ];
  }
}
