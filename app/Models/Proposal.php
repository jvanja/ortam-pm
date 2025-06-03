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
  /**
   * PdfInvoice method to customize the PDF generation.
   *
   * @return PdfInvoice
   */
  public function toPdfInvoice(): PdfProposal {
    return new PdfProposal(
      id: $this->id,
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
}
