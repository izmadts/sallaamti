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

    // Grouped as [group => [key => value]] — NOT flattened into a single
    // "group.key" string. Translation keys are natural English phrases
    // ("e.g. Multan, Lahore...", "Please fix the following:") and very
    // often contain literal periods; a flattened key fed through
    // Translator::addLines() gets re-split on EVERY dot via Arr::set(),
    // silently corrupting unrelated sibling keys into nested arrays
    // instead of strings (see applyToTranslator()'s docblock for the
    // actual injection that avoids this).
    public static function getTranslationsByLocale(string $locale): array
    {
        return Cache::rememberForever("translations_by_locale_{$locale}", function () use ($locale) {
            return static::where('locale', $locale)
                ->get()
                ->groupBy('group')
                ->map(fn ($rows) => $rows->pluck('value', 'key')->toArray())
                ->toArray();
        });
    }

    // Injects DB-stored translations directly into the Laravel translator's
    // internal $loaded array, bypassing Translator::addLines() entirely.
    // addLines() takes a flat "group.key" => value map and calls
    // Arr::set($this->loaded, "$namespace.$group.$locale.$item", $value)
    // for each — but $item there is the RAW key text, and Arr::set()
    // dot-explodes the ENTIRE path string, including every period inside
    // $item itself. A key like "e.g. Multan, Lahore..." becomes a chain of
    // nested array segments (e -> g -> ' Multan, Lahore' -> '' -> '' -> ''),
    // and depending on row processing order this silently turns OTHER,
    // unrelated scalar translation values into arrays too — which is
    // exactly what crashes Blade's {{ }} echo (htmlspecialchars() given an
    // array) on a completely unrelated key like "Find a Match".
    //
    // Translator::load() itself never has this problem — it assigns
    // $this->loaded[$namespace][$group][$locale] = $lines directly, with
    // no dot-parsing of the group's contents. This mirrors that exact safe
    // assignment via reflection, since Translator exposes no public
    // "set one group" method — only the framework-internal load() and the
    // wholesale setLoaded(array $loaded) replace-everything method.
    public static function applyToTranslator(string $locale): void
    {
        $grouped = static::getTranslationsByLocale($locale);

        if (empty($grouped)) {
            return;
        }

        $translator = app('translator');
        $property = new \ReflectionProperty($translator, 'loaded');
        $property->setAccessible(true);
        $loaded = $property->getValue($translator);

        foreach ($grouped as $group => $lines) {
            $loaded['*'][$group][$locale] = $lines;
        }

        $property->setValue($translator, $loaded);
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
