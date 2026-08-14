<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = ['locale', 'group', 'key', 'value'];

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    public static function getTranslationsByLocale(string $locale): array
    {
        return Cache::rememberForever("translations_by_locale_{$locale}", function () use ($locale) {
            return static::where('locale', $locale)
                ->get()
                ->mapWithKeys(fn ($row) => ["{$row->group}.{$row->key}" => $row->value])
                ->toArray();
        });
    }

    public static function forgetCachedTranslations(string $locale): void
    {
        Cache::forget("translations_by_locale_{$locale}");
    }

    protected static function booted(): void
    {
        static::saved(fn (self $translation) => static::forgetCachedTranslations($translation->locale));
        static::deleted(fn (self $translation) => static::forgetCachedTranslations($translation->locale));
    }
}
