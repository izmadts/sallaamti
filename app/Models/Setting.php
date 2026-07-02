<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // Get a setting value by key, with optional default
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
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
