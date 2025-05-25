<?php

namespace App\Models;

use Elegantly\Invoices\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Language;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Project extends Model {
  use HasFactory;

  protected $fillable = [
    'department',
    'type',
    'manager',
    'language',
    'address',
    'status',
    'budget',
    'currency',
    'sales_representative_name',
    'opening_date',
    'deadline',
    'client_id',
    'organization_id',
    'current_project_pipeline_stage_id',
    'notes',
  ];

  protected $casts = [
    'id' => 'string',
    'language' => Language::class,
    'status' => ProjectStatus::class,
    'opening_date' => 'date',
    'deadline' => 'date',
    'budget' => 'float',
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

  public function client(): BelongsTo {
    return $this->belongsTo(Client::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  public function updateProjectStatus(ProjectStatus $status): void {
    $this->project_status = $status;
  }

  public function invoices(): HasMany {
    return $this->hasMany(Invoice::class);
  }

  public function users(): BelongsToMany {
    return $this->belongsToMany(User::class)->withTimestamps();
  }

  /**
   * Get all pipeline stages defined for this project.
   */
  public function pipelineStages(): HasMany {
    return $this->hasMany(ProjectPipelineStage::class)->orderBy('stage_order');
  }

  /**
   * Get the current active pipeline stage for this project.
   */
  public function currentPipelineStage(): BelongsTo {
    return $this->belongsTo(ProjectPipelineStage::class, 'current_project_pipeline_stage_id');
  }

  /**
   * Get the full pipeline history for this project.
   */
  public function pipelineHistory(): HasMany {
    return $this->hasMany(ProjectPipelineHistory::class)->orderBy('changed_at');
  }

  /**
   * Get the files associated with the project.
   */
  public function files(): HasMany {
    return $this->hasMany(File::class);
  }

  // public function workload(): BelongsToMany {
  //   return $this->belongsToMany(Workload::class);
  // }
  //
  //
  // public function timeSheets(): HasMany {
  //   return $this->hasMany(TimeSheet::class);
  // }
  // public function quote(): BelongsTo {
  //   return $this->belongsTo(Quote::class);
  // }
  //
  // public function addInvoice(float $amount): Invoice {
  //   $invoice = Invoice::create([
  //     'amount' => $amount,
  //   ]);
  //   return $invoice;
  // }
}
