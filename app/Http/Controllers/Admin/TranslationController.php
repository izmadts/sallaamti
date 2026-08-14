<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::orderBy('name')->get();
        $locale = $request->get('locale', optional($languages->firstWhere('is_default', true))->language ?? 'ur');

        $translations = Translation::where('locale', $locale)
            ->orderBy('key')
            ->paginate(50)
            ->withQueryString();

        return view('admin.translations.index', compact('languages', 'locale', 'translations'));
    }

    public function fetchByLocale(string $locale)
    {
        $translations = Translation::where('locale', $locale)->orderBy('key')->get(['id', 'group', 'key', 'value']);

        return response()->json($translations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'locale' => ['required', 'string', 'exists:languages,language'],
            'group' => ['nullable', 'string', 'max:50'],
            'key' => ['required', 'string', 'max:191'],
            'value' => ['required', 'string'],
        ]);

        Translation::updateOrCreate(
            [
                'locale' => $request->locale,
                'group' => $request->input('group', 'db'),
                'key' => $request->key,
            ],
            ['value' => $request->value]
        );

        return back()->with('status', 'Translation saved.');
    }

    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'value' => ['required', 'string'],
        ]);

        $translation->update(['value' => $request->value]);

        return back()->with('status', 'Translation updated.');
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();

        return back()->with('status', 'Translation removed.');
    }
}
