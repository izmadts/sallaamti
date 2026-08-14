<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderByDesc('is_default')->orderBy('name')->get();

        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'language' => ['required', 'string', 'max:10', 'unique:languages,language'],
            'name' => ['required', 'string', 'max:50'],
            'flag' => ['nullable', 'string', 'max:10'],
            'is_rtl' => ['nullable', 'boolean'],
        ]);

        Language::create([
            'language' => $request->language,
            'name' => $request->name,
            'flag' => $request->flag,
            'is_rtl' => $request->boolean('is_rtl'),
            'is_active' => true,
            'is_default' => Language::count() === 0,
        ]);

        Language::forgetCachedLanguage();

        return back()->with('status', 'Language added.');
    }

    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'flag' => ['nullable', 'string', 'max:10'],
            'is_rtl' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $language->update([
            'name' => $request->name,
            'flag' => $request->flag,
            'is_rtl' => $request->boolean('is_rtl'),
            'is_active' => $request->boolean('is_active'),
        ]);

        Language::forgetCachedLanguage();

        return back()->with('status', 'Language updated.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete the default language.');
        }

        $language->delete();

        Language::forgetCachedLanguage();

        return back()->with('status', 'Language removed.');
    }

    public function setDefault(Language $language)
    {
        Language::setDefaultLanguage($language->id);

        return back()->with('status', "{$language->name} is now the default language.");
    }
}
