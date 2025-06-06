<?php

namespace App\Models;

use Elegantly\Invoices\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Client extends Model {
  use HasFactory;

  protected $fillable = [
    'company_name',
    'contact_person',
    'address',
    'phone',
    'email',
    'organization_id',
  ];

  protected $casts = [
    'id' => 'string',
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

  public function proposals(): HasMany {
    return $this->hasMany(Proposal::class);
  }

  public function projects(): HasMany {
    return $this->hasMany(Project::class);
  }

  public function invoices(): HasMany {
    return $this->hasMany(Invoice::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }
}
