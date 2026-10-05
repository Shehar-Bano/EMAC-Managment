<?php

namespace App\Models;

use App\Enums\OtpPurpose;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identifier',
        'purpose',
        'otp_hash',
        'attempt_count',
        'max_attempts',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => OtpPurpose::class,
            'attempt_count' => 'integer',
            'max_attempts' => 'integer',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * User associated with the OTP (if applicable).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if max attempts reached.
     */
    public function isMaxAttemptsReached(): bool
    {
        return $this->attempt_count >= $this->max_attempts;
    }

    /**
     * Check if already consumed/verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
