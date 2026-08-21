<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\Translation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

// The mobile app has no cookie for the web SetLocale middleware to read —
// it sends its chosen language explicitly as a header instead. Reuses the
// exact same Translation/Language data the website's __('db.*') strings
// already draw from, so API response messages wrapped in __('db.') are
// translated the same way the site's UI already is, with zero new
// translation content to maintain separately.
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Schema::hasTable('languages')) {
            return $next($request);
        }

        $language = null;

        if ($code = $request->header('X-Locale')) {
            $language = Language::findActiveByCode($code);
        }

        $language ??= Language::getDefaultLanguage();

        $locale = $language->language ?? 'en';

        App::setLocale($locale);

        if (Schema::hasTable('translations')) {
            $translations = Translation::getTranslationsByLocale($locale);

            if (!empty($translations)) {
                app('translator')->addLines($translations, $locale);
            }
        }

        return $next($request);
    }
}
