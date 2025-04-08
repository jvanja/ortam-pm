<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Invitation extends Model {
  use HasFactory; // Assuming you might want a factory later

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'email',
    'organization_id',
    'token',
    'inviter_id',
    'expires_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected function casts(): array {
    return [
      'expires_at' => 'datetime',
    ];
  }

  /**
   * Get the organization that this invitation belongs to.
   */
  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

  /**
   * Get the user who sent the invitation.
   */
  public function inviter(): BelongsTo {
    return $this->belongsTo(User::class, 'inviter_id');
  }

  /**
   * Check if the invitation has expired.
   */
  public function hasExpired(): bool {
    // Ensure expires_at is a Carbon instance before comparison
    return $this->expires_at instanceof Carbon && $this->expires_at->isPast();
  }
}
