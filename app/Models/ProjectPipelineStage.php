<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPipelineStage extends Model {
  // use HasFactory, HasUuids;
  use HasFactory;

  protected $fillable = [
    'project_id',
    'name',
    'stage_order',
    'is_system_default',
    'status',
    'completed_at',
    'notes',
  ];

  protected $casts = [
    'stage_order' => 'integer',
    'is_system_default' => 'boolean',
    'completed_at' => 'datetime',
  ];

  /**
   * The project this pipeline stage belongs to.
   */
  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  /**
   * History events related to this specific pipeline stage.
   */
  public function history(): HasMany {
    return $this->hasMany(ProjectPipelineHistory::class);
  }

  /**
   * Tasks that are part of this pipeline stage.
   */
  public function tasks(): HasMany {
    return $this->hasMany(TimeSheet::class);
  }

  /**
   * Check if the stage is completed.
   */
  public function isCompleted(): bool {
    return $this->status === 'completed' && $this->completed_at !== null;
  }
}
