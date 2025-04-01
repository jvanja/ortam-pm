<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model {
  use HasFactory;

  protected $fillable = [
    'invoice_number',
    'amount',
    'status',
    'project_id',
    'client_id',
    'organization_id',
  ];

  protected $casts = [
    'id' => 'string',
    'amount' => 'float',
    'status' => 'boolean',
  ];

  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  public function client(): BelongsTo {
    return $this->belongsTo(Client::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  // - TODO:
  // Move to the controller
  // public function markAsPaid(): void {
  //   $this->paid = true;
  // }
}
