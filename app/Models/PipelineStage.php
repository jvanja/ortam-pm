<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model {
  use HasFactory, HasUuids;

  protected $fillable = [
    'default_name',
    'is_system_default',
    'default_order',
  ];

  protected $casts = [
    'is_system_default' => 'boolean',
    'default_order' => 'integer',
  ];

  /**
   * Get the project stages that use this stage as a base.
   */
  public function projectPipelineStages(): HasMany {
    return $this->hasMany(ProjectPipelineStage::class);
  }
}
