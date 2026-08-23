<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Support\TranslationKeyScanner;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::orderBy('name')->get();
        $locale = $request->get('locale', optional($languages->firstWhere('is_default', true))->language ?? 'ur');
        $search = trim((string) $request->get('search', ''));

        $usedKeys = TranslationKeyScanner::scan();
        $existing = Translation::where('locale', $locale)->get()->keyBy('key');

        // One row per key actually referenced in the codebase, joined
        // against whatever's already translated for this locale.
        $rows = collect($usedKeys)->map(function (string $key) use ($existing) {
            $row = $existing->get($key);

            return (object) [
                'id' => $row?->id,
                'group' => $row?->group ?? 'db',
                'key' => $key,
                'value' => $row?->value ?? '',
                'missing' => !$row || trim($row->value) === '',
            ];
        });

        // A translated row whose key the scanner didn't find (renamed/
        // removed English text, or a key built dynamically the regex
        // can't see) shouldn't just vanish — surfaced separately so
        // nothing silently falls out of view.
        $usedKeySet = array_flip($usedKeys);
        $orphaned = $existing->reject(fn ($row) => isset($usedKeySet[$row->key]))
            ->map(fn ($row) => (object) [
                'id' => $row->id,
                'group' => $row->group,
                'key' => $row->key,
                'value' => $row->value,
                'missing' => false,
            ]);

        $rows = $rows->concat($orphaned->values());

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $rows = $rows->filter(fn ($row) => str_contains(mb_strtolower($row->key), $needle) || str_contains(mb_strtolower($row->value), $needle));
        }

        $missingCount = $rows->where('missing', true)->count();

        // Missing first (so the whole point of this page — "what still
        // needs translating" — is immediately visible), then alphabetical.
        $rows = $rows->sortBy(fn ($row) => [$row->missing ? 0 : 1, $row->key])->values();

        $translations = $this->paginate($rows, $request);

        return view('admin.translations.index', compact('languages', 'locale', 'translations', 'missingCount', 'search'));
    }

    private function paginate(Collection $rows, Request $request, int $perPage = 50): LengthAwarePaginator
    {
        $page = (int) $request->get('page', 1);

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
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

    // Codebase changes constantly (new __('db....') calls added all the
    // time) but re-scanning every file on every page load would be slow,
    // so the key list is cached — this lets an admin force a fresh scan
    // after deploying new translatable text instead of waiting 6 hours.
    public function rescan()
    {
        TranslationKeyScanner::forget();

        return back()->with('status', 'Rescanned the codebase for translation keys.');
    }
}
