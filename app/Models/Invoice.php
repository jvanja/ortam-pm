<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
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
    'status' => InvoiceStatus::class,
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

}
