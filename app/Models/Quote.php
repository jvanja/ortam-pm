<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Language;
use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model {
    use HasUuids;

    protected $fillable = [
        'project_type',
        'quote_language',
        'project_address',
        'budget',
        'sales_representative_name',
        'quote_status',
        'client_id',
    ];

    protected $casts = [
        'id' => 'string',
        'quote_language' => Language::class,
        'quote_status' => QuoteStatus::class,
        'budget' => 'float',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function project(): HasOne {
        return $this->hasOne(Project::class);
    }

    public function createQuote(): void {
        // TODO: Implement createQuote() method.
    }

    public function updateQuoteStatus(QuoteStatus $status): void {
        // TODO: Implement updateQuoteStatus() method.
    }

    public function convertToProject(): Project {
        // TODO: Implement convertToProject() method.
    }
}
