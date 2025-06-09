<?php

namespace App\Models;

use App\Enums\ProposalState;
use Elegantly\Invoices\Support\Buyer;
use Elegantly\Invoices\Support\PaymentInstruction;
use Elegantly\Invoices\Support\Seller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\PdfProposal;
use Illuminate\Support\Str;

class Proposal extends Model {
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'proposals';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'client_id',
    'project_id',
    'invoice_id',
    'organization_id',
    'title',
    'description',
    'currency',
    'subtotal_amount',
    'tax_amount',
    'total_amount',
    'state',
    'sent_at',
    'accepted_at',
    'rejected_at',
    'expires_at',
    'token', // Added token to fillable
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'sent_at' => 'datetime',
    'accepted_at' => 'datetime',
    'rejected_at' => 'datetime',
    'expires_at' => 'datetime',
    'token' => 'string', // Added token to casts
  ];

  /**
   * Get the client that owns the proposal.
   */
  public function client(): BelongsTo {
    return $this->belongsTo(Client::class);
  }

  /**
   * Get the project that the proposal belongs to.
   */
  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  /**
   * Get the invoice that was generated from this proposal.
   */
  public function invoice(): HasOne {
    return $this->hasOne(Invoice::class);
  }


  /**
   * Get the organization that the proposal belongs to.
   */
  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  public function getState(): string|ProposalState {
    return ProposalState::tryFrom($this->state) ?? $this->state;
  }

  public function buyer_information(): array {
    return [
      'name' => $this->client->company_name,
      'phone' => $this->client->phone,
      'email' => $this->client->email,
      'address' => json_decode($this->client->address, TRUE),
    ];
  }
  public function seller_information(): array {
    return [
      'name' => $this->organization->name,
      'phone' => $this->organization->phone,
      'email' => $this->organization->email,
      'address' => json_decode($this->client->address, TRUE),
    ];
  }
  /**
   * PdfInvoice method to customize the PDF generation.
   *
   * @return PdfInvoice
   */
  public function toPdfInvoice(): PdfProposal {
    return new PdfProposal(
      id: $this->id,
      buyer: Buyer::fromArray($this->buyer_information() ?? []),
      seller: Seller::fromArray($this->seller_information() ?? []),
      state: $this->getState(),
      title: $this->title,
      description: $this->description,
      currency: $this->currency,
      subtotal_amount: $this->subtotal_amount,
      tax_amount: $this->tax_amount,
      total_amount: $this->total_amount,
      sent_at: $this->sent_at,
      accepted_at: $this->accepted_at,
      rejected_at: $this->rejected_at,
      expires_at: $this->expires_at,
    );
  }

  /**
   * Generate a unique token for the proposal.
   */
  public function generateUniqueToken(): string {
    $this->token = Str::random(60); // Generate a random 60-character string
    $this->save();
    return $this->token;
  }

  /**
   * Mark the proposal as sent.
   */
  public function markAsSent(): void {
    $this->state = ProposalState::Sent->value;
    $this->sent_at = now();
    $this->save();
  }

  /**
   * Mark the proposal as viewed.
   */
  public function markAsViewed(): void {
    // Only mark as viewed if it was previously sent and not already viewed/accepted/rejected
    if ($this->state === ProposalState::Sent->value) {
      $this->state = ProposalState::Viewed->value;
      $this->save();
    }
  }
}
