<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MatchmakingLinkAccess extends Model
{
    const CREATED_AT = 'accessed_at';
    const UPDATED_AT = null;

    protected $fillable = ['subject_type', 'subject_id', 'purpose', 'ip_address', 'city', 'user_agent', 'result'];

    protected function casts(): array
    {
        return ['accessed_at' => 'datetime'];
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Passive, best-effort logging — never allowed to block or fail the
    // actual client action it's recording. City lookup is a short-timeout
    // best-effort call against a free IP-geolocation API (no signup, no
    // API key, no WhatsApp/SMS cost); if it's slow or unreachable the
    // access is still logged with just the IP.
    public static function record(Model $subject, string $purpose, Request $request, ?string $result = null): void
    {
        try {
            static::create([
                'subject_type' => $subject::class,
                'subject_id' => $subject->id,
                'purpose' => $purpose,
                'ip_address' => $request->ip(),
                'city' => static::lookupCity($request->ip()),
                'user_agent' => $request->userAgent(),
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            \Log::error('MatchmakingLinkAccess::record failed: ' . $e->getMessage());
        }
    }

    private static function lookupCity(?string $ip): ?string
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'])) {
            return null;
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,city,regionName']);

            if ($response->ok() && $response->json('status') === 'success') {
                return trim(($response->json('city') ?: '') . ', ' . ($response->json('regionName') ?: ''), ', ');
            }
        } catch (\Throwable $e) {
            // Network hiccup or the free-tier API being unavailable should
            // never stop the client's action from completing.
        }

        return null;
    }
}
