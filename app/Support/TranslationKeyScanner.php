<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

// Finds every db.* translation key actually referenced in the codebase —
// Blade views AND plain PHP (controllers, notifications, form requests —
// anywhere a hardcoded or dynamically-built message calls __('db....'))
// — so the admin Translations page can show which ones have no Urdu row
// yet, instead of only listing whatever was already manually added.
class TranslationKeyScanner
{
    private const CACHE_KEY = 'translation_used_keys';

    public static function scan(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(6), function () {
            $keys = [];

            foreach ([resource_path('views'), app_path()] as $root) {
                if (!is_dir($root)) {
                    continue;
                }

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

                foreach ($files as $file) {
                    if ($file->isDir() || $file->getExtension() !== 'php') {
                        continue;
                    }

                    $contents = file_get_contents($file->getPathname());

                    // __('db.Some string') or __("db.Some string") — a
                    // pragmatic regex over source text, not a full PHP
                    // parser, so it only catches literal string keys
                    // (the overwhelming majority of how __() is actually
                    // called in this codebase), not ones built from
                    // variables/concatenation.
                    if (preg_match_all('/__\(\s*[\'"]db\.((?:[^\'"\\\\]|\\\\.)*)[\'"]/', $contents, $matches)) {
                        foreach ($matches[1] as $key) {
                            $key = trim(stripslashes($key));

                            // Skip anything that isn't a real literal key:
                            // __("db.$var") string interpolation leaves
                            // fragments like "$label" or "$link[label]"
                            // behind that the regex can't resolve to their
                            // actual runtime value, and there's nothing
                            // translatable about punctuation-only matches.
                            if ($key === '' || str_contains($key, '$') || !preg_match('/[a-zA-Z]/', $key)) {
                                continue;
                            }

                            $keys[$key] = true;
                        }
                    }
                }
            }

            ksort($keys);

            return array_keys($keys);
        });
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
