<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    'task_performed' => Task::class,
    'worked_duration' => 'float',
  ];

  public function user(): BelongsTo {
    return $this->belongsTo(User::class);
  }

  public function project(): BelongsTo {
    return $this->belongsTo(Project::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  // - TODO:
  // Move to the controller
  // public function getTotalHours(): float {
  //   return $this->worked_duration;
  // }
}
