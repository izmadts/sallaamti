<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    protected $fillable = ['language', 'name', 'is_rtl', 'is_active', 'is_default'];

    protected $casts = [
        'is_rtl' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getDefaultLanguage(): ?self
    {
        return Cache::rememberForever('default_language', function () {
            return static::where('is_default', true)->first()
                ?? static::active()->first();
        });
    }

    public static function findActiveByCode(string $code): ?self
    {
        return static::active()->where('language', $code)->first();
    }

    public static function setDefaultLanguage(int $id): self
    {
        $language = static::findOrFail($id);

        static::where('is_default', true)->update(['is_default' => false]);
        $language->update(['is_default' => true]);

        static::forgetCachedLanguage();

        return $language;
    }

    public static function forgetCachedLanguage(): void
    {
        Cache::forget('default_language');
        Cache::forget('languages_list');
    }
}
