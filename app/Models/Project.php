<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Language;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    'sales_representative_name',
    'opening_date',
    'deadline',
    'client_id',
    'organization_id',
  ];

  protected $casts = [
    'id' => 'string',
    'language' => Language::class,
    'status' => ProjectStatus::class,
    'opening_date' => 'date',
    'deadline' => 'date',
    'budget' => 'float',
  ];

  public function client(): BelongsTo {
    return $this->belongsTo(Client::class);
  }

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  public function updateProjectStatus(ProjectStatus $status): void {
    $this->project_status = $status;
  }

  // public function workload(): BelongsToMany {
  //   return $this->belongsToMany(Workload::class);
  // }
  //
  // public function invoices(): HasMany {
  //   return $this->hasMany(Invoice::class);
  // }
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
