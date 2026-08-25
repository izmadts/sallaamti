<?php

namespace App\Support;

use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

// PIN alone is only ~10,000 combinations — far weaker than a real
// password, so it's only ever accepted on a device that's already
// proven itself with a real credential (password or social login) once.
// The cookie this reads/writes carries no user identity by itself, only
// an opaque random token; App\Http\Requests\Auth\LoginRequest looks that
// token up against the specific user being authenticated, so a stolen
// cookie alone doesn't identify whose device it is.
class DeviceTrust
{
    private const COOKIE_NAME = 'sallaamti_device';

    // ~1 year — long-lived on purpose (this is "remember this browser",
    // not "remember this session"), same spirit as the "remember me"
    // checkbox but scoped to the PIN shortcut specifically, not the
    // session itself.
    private const COOKIE_MINUTES = 60 * 24 * 365;

    public static function isTrusted(User $user, Request $request): bool
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (!$token) {
            return false;
        }

        return TrustedDevice::where('user_id', $user->id)->where('token', $token)->exists();
    }

    // Call this right after a real password (or social) login succeeds —
    // that's the proof of ownership that lets this exact browser use the
    // PIN shortcut afterward. Reuses an existing token for this device if
    // one's already on file (just bumps last_used_at) rather than piling
    // up a new row every login.
    public static function trust(User $user, Request $request): void
    {
        $existingToken = $request->cookie(self::COOKIE_NAME);

        if ($existingToken) {
            $device = TrustedDevice::where('user_id', $user->id)->where('token', $existingToken)->first();
            if ($device) {
                $device->update(['last_used_at' => now()]);
                return;
            }
        }

        $token = Str::random(64);

        TrustedDevice::create([
            'user_id' => $user->id,
            'token' => $token,
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'last_used_at' => now(),
        ]);

        Cookie::queue(self::COOKIE_NAME, $token, self::COOKIE_MINUTES, null, null, null, true);
    }
}
