<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model {
  use HasFactory, HasUuids;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'project_id',
    'name',
    'path',
    'mime_type',
    'size',
  ];

  /**
   * Get the project that owns the file.
   */
  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }
}
