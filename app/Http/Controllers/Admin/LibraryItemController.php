<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryItem;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Admin → Library: upload booklets/documents (PDF + optional cover), publish or hide them.
class LibraryItemController extends Controller
{
    public function index()
    {
        $items = LibraryItem::orderBy('category')->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.library.index', ['items' => $items, 'categories' => LibraryItem::CATEGORIES]);
    }

    public function create()
    {
        return view('admin.library.form', ['item' => new LibraryItem(['category' => 'family', 'language' => 'ur']), 'categories' => LibraryItem::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $data['file_path'] = $request->file('file')->store('library', 'public');
        if ($request->hasFile('cover')) {
            $data['cover_path'] = ImageOptimizer::store($request->file('cover'), 'library/covers', 'public', maxDimension: 900, quality: 82);
        }
        LibraryItem::create($data);

        return redirect()->route('admin.library.index')->with('status', 'Library item added.');
    }

    public function edit(LibraryItem $library)
    {
        return view('admin.library.form', ['item' => $library, 'categories' => LibraryItem::CATEGORIES]);
    }

    public function update(Request $request, LibraryItem $library)
    {
        $data = $this->validated($request, $library);
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($library->file_path);
            $data['file_path'] = $request->file('file')->store('library', 'public');
        }
        if ($request->hasFile('cover')) {
            if ($library->cover_path) Storage::disk('public')->delete($library->cover_path);
            $data['cover_path'] = ImageOptimizer::store($request->file('cover'), 'library/covers', 'public', maxDimension: 900, quality: 82);
        }
        $library->update($data);

        return redirect()->route('admin.library.index')->with('status', 'Library item updated.');
    }

    public function destroy(LibraryItem $library)
    {
        Storage::disk('public')->delete(array_filter([$library->file_path, $library->cover_path]));
        $library->delete();

        return redirect()->route('admin.library.index')->with('status', 'Library item deleted.');
    }

    public function togglePublish(LibraryItem $library)
    {
        $library->update(['is_published' => !$library->is_published]);

        return back()->with('status', $library->is_published ? 'Published on /library.' : 'Hidden from /library.');
    }

    private function validated(Request $request, ?LibraryItem $item): array
    {
        $data = $request->validate([
            'code'        => ['nullable', 'string', 'max:20'],
            'title'       => ['required', 'string', 'max:200'],
            'slug'        => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('library_items', 'slug')->ignore($item?->id)],
            'category'    => ['required', Rule::in(array_keys(LibraryItem::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'pages'       => ['nullable', 'integer', 'min:1', 'max:999'],
            'language'    => ['required', Rule::in(['ur', 'en'])],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'file'        => [$item ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:20480'],
            'cover'       => ['nullable', 'image', 'max:4096'],
        ]);
        $data['slug'] = $data['slug'] ?: (Str::slug($data['code'] . ' ' . Str::ascii($data['title'])) ?: Str::lower(Str::random(8)));
        if (!$item && LibraryItem::where('slug', $data['slug'])->exists()) {
            $data['slug'] .= '-' . Str::lower(Str::random(4));
        }
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['file'], $data['cover']);

        return $data;
    }
}
