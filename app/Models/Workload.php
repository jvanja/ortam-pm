<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Workload extends Model
{
    use HasUuids;

    protected $fillable = [
        'deadlines',
    ];

    protected $casts = [
        'id' => 'string',
        'deadlines' => 'array',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function visualizeWorkload(): GanttChart
    {
        // TODO: Implement visualizeWorkload() method.
    }

    public function updateWorkload(Project $project): void
    {
        // TODO: Implement updateWorkload() method.
    }
}
