<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Language;
use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model {
  use HasFactory;

  protected $fillable = [
    'project_type',
    'quote_language',
    'project_address',
    'budget',
    'sales_representative_name',
    'quote_status',
    'project_id',
    'client_id',
    'organization_id',
  ];

  protected $casts = [
    'id' => 'string',
    'quote_language' => Language::class,
    'quote_status' => QuoteStatus::class,
    'budget' => 'float',
  ];

  public function updateQuoteStatus(QuoteStatus $status): void {
    $this->quote_status = $status;
  }

  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }
}
