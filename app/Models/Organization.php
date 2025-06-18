<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model {
  use HasFactory;

  protected $fillable = [
    'name',
    'email',
    'phone',
    'address',
    'logo',
    'brand_color',
    'payment_instructions',
  ];

  public function projects(): HasMany {
    return $this->hasMany(Project::class);
  }

  public function clients(): HasMany {
    return $this->hasMany(Client::class);
  }

  public function users(): HasMany {
    return $this->hasMany(User::class);
  }
}
