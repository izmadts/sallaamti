<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Server-side counterpart to the client-side dataLayer push already in
// partials/gtm-body.blade.php (session('conversion_event') → GTM → whatever
// Pixel tag is configured in the GTM container itself). That path only ever
// fires from a page load in the member's own browser, right after they took
// the action — it can't reach an event that happens later from somewhere
// else entirely, e.g. an admin confirming a payment days after the member
// left the site, and it's invisible to anyone running an ad blocker. This
// calls Meta's Graph API directly instead, with hashed user data for
// Advanced Matching, same event names Meta's standard-events set expects
// (CompleteRegistration, Purchase, ...) so both paths attribute to the same
// conversion in Ads Manager.
class MetaConversionsApi
{
    private const API_VERSION = 'v21.0';

    /**
     * @param array<string, mixed> $customData e.g. ['value' => 500, 'currency' => 'PKR']
     */
    public function send(string $eventName, ?User $user = null, array $customData = [], ?Request $request = null): void
    {
        $pixelId = setting('meta_pixel_id');
        $accessToken = setting('meta_conversions_api_token');

        // Silently a no-op until admin fills both Settings fields in —
        // every call site treats this the same as any other optional
        // ad-attribution signal, never a hard dependency.
        if (!$pixelId || !$accessToken) {
            return;
        }

        $userData = [];
        if ($user?->email) {
            $userData['em'] = [$this->hash($user->email)];
        }
        if ($user?->phone) {
            $userData['ph'] = [$this->hash($this->normalizePhone($user->phone))];
        }
        if ($request) {
            $userData['client_ip_address'] = $request->ip();
            $userData['client_user_agent'] = $request->userAgent();
            // Meta's own browser-set cookies — present when the visitor
            // arrived with the client-side Pixel already loaded (e.g. via
            // GTM on an earlier page), and the single strongest match
            // signal available short of a login identifier.
            if ($request->cookie('_fbp')) {
                $userData['fbp'] = $request->cookie('_fbp');
            }
            if ($request->cookie('_fbc')) {
                $userData['fbc'] = $request->cookie('_fbc');
            }
        }

        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => now()->timestamp,
                'action_source' => 'website',
                'event_source_url' => $request?->fullUrl() ?? url('/'),
                'user_data' => $userData,
                'custom_data' => $customData,
            ]],
            'access_token' => $accessToken,
        ];

        try {
            $response = Http::timeout(5)->asJson()
                ->post("https://graph.facebook.com/" . self::API_VERSION . "/{$pixelId}/events", $payload);

            if ($response->failed()) {
                Log::warning('MetaConversionsApi: ' . $eventName . ' failed — ' . $response->body());
            }
        } catch (\Throwable $e) {
            // Ad attribution must never break the real action it's attached
            // to (a payment confirm, a profile submit) — same reasoning as
            // every notify() call in this codebase being wrapped the same way.
            Log::warning('MetaConversionsApi: ' . $eventName . ' threw — ' . $e->getMessage());
        }
    }

    private function hash(string $value): string
    {
        return hash('sha256', strtolower(trim($value)));
    }

    // Matches WhatsappNotifier's own normalizePhone() — Meta expects E.164
    // digits (country code, no '+', no formatting) before hashing, same
    // "known gap for a purely local-format number" caveat applies here too.
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? $phone;
    }
}
