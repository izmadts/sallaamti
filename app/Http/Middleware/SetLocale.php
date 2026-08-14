<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\Translation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Schema::hasTable('languages')) {
            return $next($request);
        }

        $language = null;

        if ($cookieCode = $request->cookie('language')) {
            $language = Language::findActiveByCode($cookieCode);
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

        view()->share('isRtl', (bool) ($language->is_rtl ?? false));
        view()->share('activeLanguage', $language);

        return $next($request);
    }
}
