<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Support\Facades\Storage;

// Public Sallaamti Library: free PDFs to read, download and share.
class LibraryController extends Controller
{
    public function index()
    {
        $items = LibraryItem::published()->orderBy('sort_order')->orderBy('id')->get()->groupBy('category');

        return view('library.index', ['groups' => $items, 'categories' => LibraryItem::CATEGORIES]);
    }

    public function show(LibraryItem $libraryItem)
    {
        abort_unless($libraryItem->is_published, 404);
        $more = LibraryItem::published()->where('id', '!=', $libraryItem->id)->orderBy('sort_order')->limit(4)->get();

        return view('library.show', ['item' => $libraryItem, 'more' => $more]);
    }

    // Counts downloads, then streams the PDF with a readable file name.
    public function download(LibraryItem $libraryItem)
    {
        abort_unless($libraryItem->is_published && Storage::disk('public')->exists($libraryItem->file_path), 404);
        $libraryItem->increment('download_count');

        return Storage::disk('public')->download($libraryItem->file_path, 'Sallaamti-' . $libraryItem->slug . '.pdf');
    }
}
