<?php

namespace Database\Factories;

use App\Enums\ProjectType;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory {
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Proposal::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {

    $subtotal = $this->faker->randomFloat(null, 100, 5000);
    $taxAmount = $this->faker->randomElement([10, 20, 30]);
    $totalAmount = $subtotal + $taxAmount;

    $state = $this->faker->randomElement(['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired']);
    $sentAt = null;
    $acceptedAt = null;
    $rejectedAt = null;
    $expiresAt = null;

    if (in_array($state, ['sent', 'viewed', 'accepted', 'rejected', 'expired'])) {
      // $sentAt = $this->faker->dateTimeBetween('-1 year', 'now');
      $currentSystemTime = new \DateTime();
      $sentAt = $this->faker->dateTimeBetween('-1 year', $currentSystemTime);
      $expiresAt = $this->faker->dateTimeBetween($sentAt, $sentAt->modify('+3 months'));
    }
    if ($state === 'accepted' && $sentAt) {
      // $acceptedAt = $this->faker->dateTimeBetween($sentAt, 'now');
    }
    if ($state === 'rejected' && $sentAt) {
      // $rejectedAt = $this->faker->dateTimeBetween($sentAt, 'now');
    }

    return [
      'title' => fake()->randomElement(ProjectType::cases()),
      'description' => '<h1>Scope</h1>Below is a high-level breakdown of deliverables, their estimated times. <h1>Deliverables</h1>Consequat culpa pariatur proident elit. <ol> <li>Lorem ipsum dolor sit amet</li> <li>consectetur adipisicing elit</li> <li>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li> </ol> <h1>Payment</h1>Consequat culpa pariatur proident elit. Deserunt eu sit cupidatat sint Lorem proident Lorem laboris dolore eiusmod proident laborum ea minim consequat. Laboris do ad aute non. Labore fugiat velit pariatur et culpa do adipisicing enim sit exercitation. Irure sunt aliqua et est qui ipsum cillum eu laborum dolor reprehenderit do sit laborum nulla. In fugiat esse proident eiusmod laborum laboris quis nostrud voluptate cillum ullamco magna excepteur.',
      'currency' => $this->faker->currencyCode,
      'subtotal_amount' => $subtotal,
      'tax_amount' => $taxAmount,
      'total_amount' => $totalAmount,
      'state' => $state,
      'sent_at' => $sentAt,
      'accepted_at' => $acceptedAt,
      'rejected_at' => $rejectedAt,
      'expires_at' => $expiresAt,
      'invoice_id' => null,
      'project_id' => Project::inRandomOrder()->first(),
      'client_id' => Client::inRandomOrder()->first(),
      'organization_id' => Organization::inRandomOrder()->first(),
    ];
  }
}
