<?php

namespace App\Http\Controllers;

use App\Models\Language;

class LanguageSwitchController extends Controller
{
    public function switch(string $code)
    {
        $language = Language::findActiveByCode($code);

        if (!$language) {
            return back();
        }

        return back()->withCookie(cookie('language', $language->language, 60 * 24 * 365));
    }
}
