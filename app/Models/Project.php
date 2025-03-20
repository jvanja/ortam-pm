<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Language;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model {
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'project_type',
        'department',
        'project_manager',
        'project_language',
        'project_address',
        'budget',
        'sales_representative_name',
        'project_status',
        'project_opening_date',
        'deadline',
        'client_id',
    ];

    protected $casts = [
        'id' => 'string',
        'project_language' => Language::class,
        'project_status' => ProjectStatus::class,
        'project_opening_date' => 'date',
        'deadline' => 'date',
        'budget' => 'float',
    ];

    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class);
    }

    public function timeSheets(): HasMany {
        return $this->hasMany(TimeSheet::class);
    }

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class);
    }

    public function workload(): BelongsToMany {
        return $this->belongsToMany(Workload::class);
    }

    public function quote(): BelongsTo {
        return $this->belongsTo(Quote::class);
    }

    public function updateProjectStatus(ProjectStatus $status): void {
        $this->project_status = $status;
    }

    public function addInvoice(float $amount): Invoice {
        $invoice = Invoice::create([
            'amount' => $amount,
        ]);
        return $invoice;
    }
}
