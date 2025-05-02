<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPipelineStage extends Model {
  use HasFactory, HasUuids;

  protected $fillable = [
    'project_id',
    'pipeline_stage_id',
    'name',
    'stage_order',
    'status',
    'completed_at',
    'notes',
  ];

  protected $casts = [
    'stage_order' => 'integer',
    'completed_at' => 'datetime',
  ];

  /**
   * The project this pipeline stage belongs to.
   */
  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  /**
   * The default stage definition this stage might be based on.
   */
  public function pipelineStage(): BelongsTo {
    return $this->belongsTo(PipelineStage::class);
  }

  /**
   * History events related to this specific pipeline stage.
   */
  public function history(): HasMany {
    return $this->hasMany(ProjectPipelineHistory::class);
  }

  /**
   * Check if the stage is completed.
   */
  public function isCompleted(): bool {
    return $this->status === 'completed' && $this->completed_at !== null;
  }
}
