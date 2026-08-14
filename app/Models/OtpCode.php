<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpCode extends Model
{
    protected $fillable = ['user_id', 'identifier', 'code', 'purpose', 'expires_at', 'consumed_at', 'attempts'];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public const MAX_ATTEMPTS = 5;
    public const EXPIRY_MINUTES = 10;

    public static function generateFor(string $identifier, string $purpose, ?int $userId = null): self
    {
        return static::create([
            'user_id' => $userId,
            'identifier' => $identifier,
            'code' => (string) random_int(100000, 999999),
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes(self::EXPIRY_MINUTES),
        ]);
    }

    public static function verify(string $identifier, string $code, string $purpose): bool
    {
        $otp = static::where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otp || $otp->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $otp->increment('attempts');

        if ($otp->expires_at->isPast() || !hash_equals($otp->code, $code)) {
            return false;
        }

        $otp->update(['consumed_at' => Carbon::now()]);

        return true;
    }
}
