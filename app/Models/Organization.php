<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model {
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function projects(): BelongsToMany {
        return $this->belongsToMany(Project::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
