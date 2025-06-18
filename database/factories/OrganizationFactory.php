<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class OrganizationFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'name' => fake()->company(),
      'email' => fake()->safeEmail(),
      'phone' => fake()->phoneNumber(),
      'address' => json_encode([
        'street' => fake()->streetAddress(),
        'city' => fake()->city(),
        'postal_code' => fake()->postcode(),
        'state' => fake()->state(),
        'country' => 'United States',
      ]),
      'logo' => 'https://github.githubassets.com/assets/GitHub-Mark-ea2971cee799.png',
      'brand_color' => '#ff00ff',
      'payment_instructions' => '',
    ];
  }
}
