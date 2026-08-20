<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // Security audit finding: OAuth client secrets and payment account
    // numbers were stored in plaintext, unlike SocialAccount's access/
    // refresh tokens which are already encrypted — a DB-only compromise
    // (backup leak, a future injection) would otherwise hand over every
    // live API secret and bank/JazzCash account detail in cleartext.
    // Titles/labels (e.g. bank_account_title) aren't included — only the
    // actual secret/account-number values.
    private const ENCRYPTED_KEYS = [
        'google_client_secret', 'facebook_client_secret', 'tiktok_login_client_secret',
        'facebook_posting_client_secret', 'twitter_client_secret', 'youtube_client_secret',
        'tiktok_client_secret', 'threads_client_secret',
        'jazzcash_number', 'easypaisa_number', 'bank_account_number', 'bank_account_iban',
    ];

    // Per-request memoization on top of the cross-request cache — a page can
    // call setting() a dozen+ times (nav, footer, meta tags), and without this
    // each call was its own round trip to the cache store.
    protected static ?array $cachedAll = null;

    // Get a setting value by key, with optional default.
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        // Key not in the bulk snapshot (e.g. row created after the snapshot
        // was cached) — look it up once and refresh the snapshot for next time.
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        static::forgetCache();

        return static::maybeDecrypt($key, $setting->value);
    }

    protected static function allCached(): array
    {
        return static::$cachedAll ??= Cache::rememberForever('settings_all', fn () => static::allKeyed());
    }

    protected static function forgetCache(): void
    {
        static::$cachedAll = null;
        Cache::forget('settings_all');
    }

    // Set a setting value (creates or updates)
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => static::maybeEncrypt($key, $value), 'group' => $group]
        );

        static::forgetCache();
    }

    // Get all settings as a flat key-value array — the single source of
    // truth both allCached() (bulk cache) and SettingsController's edit-
    // form prefill go through, so encrypted values are transparently
    // decrypted here rather than in every caller.
    public static function allKeyed(): array
    {
        return static::all()
            ->pluck('value', 'key')
            ->map(fn ($value, $key) => static::maybeDecrypt($key, $value))
            ->toArray();
    }

    private static function maybeEncrypt(string $key, mixed $value): mixed
    {
        if (!in_array($key, self::ENCRYPTED_KEYS, true) || $value === null || $value === '') {
            return $value;
        }

        return Crypt::encryptString((string) $value);
    }

    private static function maybeDecrypt(string $key, mixed $value): mixed
    {
        if (!in_array($key, self::ENCRYPTED_KEYS, true) || $value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // A legacy plaintext value saved before encryption was added —
            // return as-is; the next save through set() encrypts it.
            return $value;
        }
    }
}
