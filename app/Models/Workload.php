<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Workload extends Model {
  use HasUuids;
  use HasFactory;

  protected $fillable = [
    'deadlines',
  ];

  protected $casts = [
    'id' => 'string',
    'deadlines' => 'array',
  ];

  public function projects(): BelongsToMany {
    return $this->belongsToMany(Project::class);
  }

  public function user(): BelongsTo {
    return $this->belongsTo(User::class);
  }
}
