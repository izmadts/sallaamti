<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * @param string|null $eventId SEO Agent backlog #7 (15 Nov 2026): pass the
     *        SAME id to the browser Pixel's eventID option (fbq('track', ...,
     *        {eventID: ...})) for the same user action, so Meta dedupes the
     *        two instead of double-counting a conversion that fired from
     *        both the member's own browser and this server call. Omit it
     *        (e.g. the admin-confirms-payment-days-later Purchase call,
     *        which has no browser counterpart at all) and one is generated
     *        anyway — Meta requires every event to have one, dedup or not.
     * @return string the event_id actually sent, so the caller can pass the
     *         same one to session('conversion_event_data') for the GTM
     *         dataLayer push in partials/gtm-body.blade.php to pick up.
     */
    public function send(string $eventName, ?User $user = null, array $customData = [], ?Request $request = null, ?string $eventId = null): string
    {
        $eventId ??= (string) Str::uuid();

        $pixelId = setting('meta_pixel_id');
        $accessToken = setting('meta_conversions_api_token');

        // Silently a no-op until admin fills both Settings fields in —
        // every call site treats this the same as any other optional
        // ad-attribution signal, never a hard dependency.
        if (!$pixelId || !$accessToken) {
            return $eventId;
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
                'event_id' => $eventId,
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

        return $eventId;
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
