<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

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

        return $setting->value;
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
            ['value' => $value, 'group' => $group]
        );

        static::forgetCache();
    }

    // Get all settings as a flat key-value array
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
