<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
  // You might also add methods for items within the proposal, e.g.,
  // public function items(): HasMany
  // {
  //     return $this->hasMany(InvoiceItem::class);
  // }
}
