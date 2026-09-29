<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Invisible bot check (reCAPTCHA v3 — no puzzle, no checkbox, nothing a real
// visitor ever sees or interacts with) for the public contact and newsletter
// forms. Degrades to "pass" when no keys are configured yet, so the forms
// keep working the moment this ships and simply get stricter once an admin
// pastes in a site/secret key pair — same on/off-until-configured pattern as
// google_mobile_client_ids elsewhere in Settings.
class Recaptcha
{
    public static function enabled(): bool
    {
        return filled(Setting::get('recaptcha_site_key')) && filled(Setting::get('recaptcha_secret_key'));
    }

    public static function siteKey(): ?string
    {
        return Setting::get('recaptcha_site_key') ?: null;
    }

    // A missing/failed verification call never blocks a real submission —
    // only an explicit low score or explicit failure from Google does. The
    // honeypot + rate limiting already in place are the fallback if this
    // service is unreachable.
    public static function verify(?string $token, string $action, float $minScore = 0.5): bool
    {
        if (!self::enabled()) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => Setting::get('recaptcha_secret_key'),
                'response' => $token,
            ])->json();
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification request failed: ' . $e->getMessage());
            return true;
        }

        if (!($response['success'] ?? false)) {
            return false;
        }

        if (($response['action'] ?? $action) !== $action) {
            return false;
        }

        return ($response['score'] ?? 0) >= $minScore;
    }
}
