<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TimeSheet extends Model {
  use HasFactory;

  protected $fillable = [
    'task_performed',
    'worked_duration',
    'user_id',
    'project_id',
    'organization_id',
  ];

  protected $casts = [
    'id' => 'string',
    'worked_duration' => 'float',
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

  public function user(): BelongsTo {
    return $this->belongsTo(User::class);
  }

  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  public function pipelineStage(): BelongsTo {
    return $this->belongsTo(ProjectPipelineStage::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }
}
