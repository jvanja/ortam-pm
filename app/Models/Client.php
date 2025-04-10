<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

  public function quotes(): HasMany {
    return $this->hasMany(Quote::class);
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
