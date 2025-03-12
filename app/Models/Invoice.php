<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model {
    use HasUuids;

    protected $fillable = [
        'project_id',
        'amount',
        'paid',
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'float',
        'paid' => 'boolean',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class);
    }

    public function markAsPaid(): void {
        $this->paid = true;
    }
}
