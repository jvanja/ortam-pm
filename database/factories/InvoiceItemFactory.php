<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {

    return [
      'unit_price' =>fake()->numberBetween(1000, 100000),
      'quantity' => fake()->numberBetween(1, 10),
      'description' => fake()->sentence(),
      'invoice_id' => fake()->randomElement(Invoice::all()->pluck('id')->toArray()),
    ];
  }
}
