<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyContent;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;

class DailyContentController extends Controller
{
    public function index()
    {
        $dailyContents = DailyContent::orderBy('id')->get();
        return view('admin.daily-content.index', compact('dailyContents'));
    }

    public function create()
    {
        return view('admin.daily-content.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:ayah,hadith'],
            'arabic_text' => ['nullable', 'string'],
            'translation' => ['required', 'string'],
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['translation'] = HtmlSanitizer::clean($validated['translation']);

        DailyContent::create($validated);

        return redirect()->route('admin.daily-content.index')->with('status', 'Daily content added.');
    }

    public function edit(DailyContent $daily_content)
    {
        return view('admin.daily-content.edit', ['dailyContent' => $daily_content]);
    }

    public function update(Request $request, DailyContent $daily_content)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:ayah,hadith'],
            'arabic_text' => ['nullable', 'string'],
            'translation' => ['required', 'string'],
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['translation'] = HtmlSanitizer::clean($validated['translation']);

        $daily_content->update($validated);

        return redirect()->route('admin.daily-content.index')->with('status', 'Daily content updated.');
    }

    public function destroy(DailyContent $daily_content)
    {
        $daily_content->delete();
        return back()->with('status', 'Daily content deleted.');
    }

    public function toggle(DailyContent $daily_content)
    {
        $daily_content->update(['is_active' => !$daily_content->is_active]);
        return back()->with('status', 'Updated.');
    }
}
