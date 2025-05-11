<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

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

  /**
   * The "booted" method of the model.
   *
   * @return void
   */
  protected static function booted() {
    static::addGlobalScope('organization', function (Builder $builder) {
      if (Auth::check()) {
        $builder->where('organization_id', Auth::user()->organization_id);
      }
    });
  }

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
