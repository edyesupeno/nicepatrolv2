<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    
    // Disable auto-incrementing ID
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'email';

    protected $fillable = [
        'email',
        'phone',
        'token',
        'otp_code',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if token is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Generate OTP code
     */
    public static function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create new password reset token
     */
    public static function createToken(string $email, ?string $phone = null): self
    {
        // Delete existing tokens for this email/phone
        static::where('email', $email)->delete();
        if ($phone) {
            static::where('phone', $phone)->delete();
        }

        return static::create([
            'email' => $email,
            'phone' => $phone,
            'token' => bin2hex(random_bytes(32)),
            'otp_code' => static::generateOTP(),
            'expires_at' => Carbon::now()->addMinutes(15), // 15 minutes expiry
        ]);
    }

    /**
     * Find valid token by email and OTP
     */
    public static function findValidToken(string $email, string $otp): ?self
    {
        return static::where('email', $email)
            ->where('otp_code', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Clean expired tokens
     */
    public static function cleanExpired(): void
    {
        static::where('expires_at', '<', Carbon::now())->delete();
    }
}
