<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model {
  /** @use HasFactory<\Database\Factories\InvoiceItemFactory> */
  use HasFactory;

  protected $fillable = [
    'quantity',
    'unit_price',
    'description',
    'invoice_id',
  ];

  public function invoice(): BelongsTo {
    return $this->belongsTo(Invoice::class);
  }
}
