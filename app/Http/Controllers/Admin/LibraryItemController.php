<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyMembersOfNewLibraryItemJob;
use App\Models\LibraryItem;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $file = $request->file('file');
        $data['file_path'] = $file->store('library', 'public');
        $data['file_size'] = $file->getSize();
        if ($request->hasFile('cover')) {
            $data['cover_path'] = ImageOptimizer::store($request->file('cover'), 'library/covers', 'public', maxDimension: 900, quality: 82);
        }

        $item = LibraryItem::create($data);
        // published_at/notified_at are deliberately not in $fillable (an
        // admin-controlled timestamp, not something update($request-shaped
        // array) should ever touch) — forceFill() is how every other such
        // flag in this codebase gets set (see User::is_active's same fix
        // this session). Plain update() here would silently no-op.
        if ($item->is_published) {
            $item->forceFill(['published_at' => now()])->save();
        }
        $this->maybeNotify($item, $request);

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
            $file = $request->file('file');
            $data['file_path'] = $file->store('library', 'public');
            $data['file_size'] = $file->getSize();
            // A re-upload is new content under the same item — the app's
            // offline copy must re-download it, not trust the old one.
            $data['version'] = $library->version + 1;
        }
        if ($request->hasFile('cover')) {
            if ($library->cover_path) Storage::disk('public')->delete($library->cover_path);
            $data['cover_path'] = ImageOptimizer::store($request->file('cover'), 'library/covers', 'public', maxDimension: 900, quality: 82);
        }
        $library->update($data);
        if ($library->is_published && !$library->published_at) {
            $library->forceFill(['published_at' => now()])->save();
        }
        $this->maybeNotify($library, $request);

        return redirect()->route('admin.library.index')->with('status', 'Library item updated.');
    }

    public function destroy(LibraryItem $library)
    {
        // A published booklet may already be shared/downloaded widely —
        // pulling it needs a real admin's call, not a staff blogger's.
        abort_if($library->is_published && !Auth::user()->hasRole('admin'), 403, 'Only an admin can delete a published library item. Hide it first.');

        Storage::disk('public')->delete(array_filter([$library->file_path, $library->cover_path]));
        $library->delete();

        return redirect()->route('admin.library.index')->with('status', 'Library item deleted.');
    }

    // Published + the "Send notification" checkbox + never notified before
    // = queue the fan-out once and mark it sent immediately, so re-saving
    // the same item (a typo fix, a new cover) never double-notifies.
    private function maybeNotify(LibraryItem $item, Request $request): void
    {
        if ($item->is_published && $request->boolean('notify', true) && !$item->notified_at) {
            NotifyMembersOfNewLibraryItemJob::dispatch($item->id);
            $item->forceFill(['notified_at' => now()])->save();
        }
    }

    public function togglePublish(LibraryItem $library)
    {
        $nowPublished = !$library->is_published;
        $library->forceFill([
            'is_published' => $nowPublished,
            // Only set the first time — hiding and republishing later must
            // not reset the "New" badge window or the sitemap lastmod.
            'published_at' => $nowPublished && !$library->published_at ? now() : $library->published_at,
        ])->save();

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
            'review_note' => ['nullable', 'string', 'max:255'],
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
