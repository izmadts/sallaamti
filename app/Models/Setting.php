<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // Get a setting value by key, with optional default.
    // Only the row's actual value is cached forever — an unseeded key is
    // never cached, so a later call with the real default (or once the row
    // is created) isn't stuck behind whatever default happened to be passed
    // by the first caller.
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "setting_{$key}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        Cache::forever($cacheKey, $setting->value);

        return $setting->value;
    }

    // Set a setting value (creates or updates)
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        // Clear cache so next get() reads fresh value
        Cache::forget("setting_{$key}");
    }

    // Get all settings as a flat key-value array
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
