<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
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
    // Ensure dependent models exist or create them
    $client = Client::factory()->create();
    $project = Project::factory()->create();

    $subtotal = $this->faker->randomFloat(2, 100, 5000);
    $taxAmount = $this->faker->randomFloat(2, 100, 5000);
    $totalAmount = $subtotal + $taxAmount;

    $state = $this->faker->randomElement(['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired']);
    $sentAt = null;
    $acceptedAt = null;
    $rejectedAt = null;
    $expiresAt = null;
    $invoiceId = null;

    if (in_array($state, ['sent', 'viewed', 'accepted', 'rejected', 'expired'])) {
      $sentAt = $this->faker->dateTimeBetween('-1 year', 'now');
      $expiresAt = $this->faker->dateTimeBetween($sentAt, $sentAt->modify('+3 months'));
    }
    if ($state === 'accepted') {
      $acceptedAt = $this->faker->dateTimeBetween($sentAt, 'now');
      // Optionally create an invoice if the proposal is accepted
      // $invoice = Invoice::factory()->create(['proposal_id' => null]); // Create without linking first
      // $invoiceId = $invoice->id;
    }
    if ($state === 'rejected') {
      $rejectedAt = $this->faker->dateTimeBetween($sentAt, 'now');
    }

    return [
      'client_id' => $client->id,
      'project_id' => $this->faker->boolean(70) ? $project->id : null, // 70% chance of having a project
      'title' => $this->faker->sentence(3) . ' Proposal',
      'description' => $this->faker->paragraph(3),
      'currency' => $this->faker->currencyCode,
      'subtotal_amount' => $subtotal,
      'tax_amount' => $taxAmount,
      'total_amount' => $totalAmount,
      'state' => $state,
      'sent_at' => $sentAt,
      'accepted_at' => $acceptedAt,
      'rejected_at' => $rejectedAt,
      'expires_at' => $expiresAt,
      'invoice_id' => $invoiceId,
    ];
  }

  /**
   * Indicate that the proposal is in a 'sent' state.
   */
  public function sent(): Factory {
    return $this->state(function (array $attributes) {
      $sentAt = $this->faker->dateTimeBetween('-1 year', 'now');
      return [
        'state' => 'sent',
        'sent_at' => $sentAt,
        'expires_at' => $this->faker->dateTimeBetween($sentAt, $sentAt->modify('+3 months')),
      ];
    });
  }

  /**
   * Indicate that the proposal is in an 'accepted' state.
   */
  public function accepted(): Factory {
    return $this->state(function (array $attributes) {
      $sentAt = $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-1 year', '-1 month');
      $acceptedAt = $this->faker->dateTimeBetween($sentAt, 'now');
      // Create an invoice and link it
      $invoice = Invoice::factory()->create(); // Ensure InvoiceFactory exists
      return [
        'state' => 'accepted',
        'sent_at' => $sentAt,
        'accepted_at' => $acceptedAt,
        'expires_at' => $this->faker->dateTimeBetween($sentAt, $sentAt->modify('+3 months')),
        'invoice_id' => $invoice->id,
      ];
    });
  }

  /**
   * Indicate that the proposal is in a 'rejected' state.
   */
  public function rejected(): Factory {
    return $this->state(function (array $attributes) {
      $sentAt = $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-1 year', '-1 month');
      return [
        'state' => 'rejected',
        'sent_at' => $sentAt,
        'rejected_at' => $this->faker->dateTimeBetween($sentAt, 'now'),
        'expires_at' => $this->faker->dateTimeBetween($sentAt, $sentAt->modify('+3 months')),
      ];
    });
  }

  /**
   * Indicate that the proposal is in an 'expired' state.
   */
  public function expired(): Factory {
    return $this->state(function (array $attributes) {
      $sentAt = $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-2 years', '-6 months');
      return [
        'state' => 'expired',
        'sent_at' => $sentAt,
        'expires_at' => $this->faker->dateTimeBetween($sentAt->modify('-2 months'), $sentAt->modify('-1 month')), // Expired in the past
      ];
    });
  }
}
