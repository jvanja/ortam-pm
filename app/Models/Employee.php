<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasUuids;

    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'department',
        'entry_date',
    ];

    protected $casts = [
        'id' => 'string',
        'entry_date' => 'date',
    ];

    public function timeSheets(): HasMany
    {
        return $this->hasMany(TimeSheet::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function workload(): BelongsTo
    {
        return $this->belongsTo(Workload::class);
    }

    public function addEmployee(): void
    {
        // TODO: Implement addEmployee() method.
    }

    public function updateEmployeeInfo(): void
    {
        // TODO: Implement updateEmployeeInfo() method.
    }

    public function logTimeSheet(Task $task, string $projectId, float $duration): TimeSheet
    {
        // TODO: Implement logTimeSheet() method.
    }
}
