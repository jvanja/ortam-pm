<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model {
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'address',
        'phone',
        'email',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function quotes(): HasMany {
        return $this->hasMany(Quote::class);
    }

    public function projects(): HasMany {
        return $this->hasMany(Project::class);
    }

    public function submitQuote(): Quote {
        // TODO: Implement submitQuote() method.
    }

    public function viewReports(): void {
        // TODO: Implement viewReports() method.
    }
}
