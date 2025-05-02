<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPipelineHistory extends Model {
  use HasFactory, HasUuids;

  // Disable automatic timestamps if only using changed_at
  public $timestamps = false;

  protected $fillable = [
    'project_id',
    'project_pipeline_stage_id',
    'action',
    'details',
    'user_id',
    'changed_at',
  ];

  protected $casts = [
    'changed_at' => 'datetime',
  ];

  protected $table = 'project_pipeline_history'; // Explicitly define table name if needed


  /**
   * The project this history event belongs to.
   */
  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  /**
   * The specific pipeline stage this history event refers to.
   */
  public function projectPipelineStage(): BelongsTo {
    return $this->belongsTo(ProjectPipelineStage::class);
  }

  /**
   * The user who triggered this history event.
   */
  public function user(): BelongsTo {
    return $this->belongsTo(User::class);
  }
}
