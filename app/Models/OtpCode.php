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

    // Checks every still-outstanding (unconsumed, unexpired, not yet
    // attempt-maxed) code for this identifier+purpose, not just the most
    // recently generated one. generateFor() never invalidates a prior
    // code when a new one is issued — e.g. tapping "Resend code" — so
    // without this, a genuinely correct, still-fresh code from an earlier
    // email would get rejected the instant a newer one existed, since the
    // old lookup only ever looked at the latest row.
    public static function verify(string $identifier, string $code, string $purpose): bool
    {
        $candidates = static::where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->get();

        $attemptable = $candidates->filter(
            fn (self $otp) => $otp->attempts < self::MAX_ATTEMPTS && $otp->expires_at->isFuture()
        );

        if ($attemptable->isEmpty()) {
            return false;
        }

        foreach ($attemptable as $otp) {
            if (hash_equals($otp->code, $code)) {
                $otp->update(['consumed_at' => Carbon::now()]);

                return true;
            }
        }

        // No match against any outstanding code — count it as a failed
        // attempt on the most recently issued one, since that's the code
        // the user most likely just received.
        $attemptable->first()->increment('attempts');

        return false;
    }
}
