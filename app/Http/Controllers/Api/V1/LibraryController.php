<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LibraryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Member-app Digital Library — read-only, no login needed (these PDFs are
// free to everyone, same as the public /library web page this mirrors).
// Every method only ever sees published items; an unpublished slug 404s
// the same way LibraryController (web) already does.
class LibraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LibraryItem::published()->orderBy('sort_order')->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate(20)->withQueryString();

        return response()->json([
            'items' => collect($items->items())->map(fn ($item) => $this->cardPayload($item)),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'total' => $items->total(),
        ]);
    }

    public function categories(): JsonResponse
    {
        $counts = LibraryItem::published()->selectRaw('category, count(*) as count')->groupBy('category')->pluck('count', 'category');

        $categories = collect(LibraryItem::CATEGORIES)
            ->map(fn ($cat, $key) => [
                'key' => $key,
                'label_ur' => $cat['ur'],
                'label_en' => $cat['en'],
                'icon' => $cat['icon'],
                'count' => $counts->get($key, 0),
            ])
            ->filter(fn ($cat) => $cat['count'] > 0)
            ->values();

        return response()->json(['categories' => $categories]);
    }

    public function show(LibraryItem $libraryItem): JsonResponse
    {
        abort_unless($libraryItem->is_published, 404);

        return response()->json(['item' => $this->cardPayload($libraryItem, detailed: true)]);
    }

    // Throttled (routes/api.php: 30/min) — a counter, not anything that
    // needs auth, so the only real abuse risk is spamming the increment.
    public function event(Request $request, LibraryItem $libraryItem): JsonResponse
    {
        abort_unless($libraryItem->is_published, 404);

        $data = $request->validate([
            'type' => ['required', Rule::in(['view', 'download', 'share'])],
        ]);

        $libraryItem->increment($data['type'] . '_count');

        return response()->json(['message' => 'ok']);
    }

    private function cardPayload(LibraryItem $item, bool $detailed = false): array
    {
        $data = [
            'id' => $item->id,
            'code' => $item->code,
            'title' => $item->title,
            'slug' => $item->slug,
            'category' => $item->category,
            'description' => $item->description,
            'cover_url' => $item->coverUrl(),
            'pages' => $item->pages,
            'file_size' => $item->file_size,
            'language' => $item->language,
            'version' => $item->version,
            'published_at' => $item->published_at?->toIso8601String(),
            'is_new' => $item->isNew(),
        ];

        if ($detailed) {
            $data['review_note'] = $item->review_note;
            // Direct asset URL on the public disk — served by the webserver
            // itself (not through PHP), so Range requests for the in-app
            // PDF reader's page-by-page streaming already work for free.
            $data['pdf_url'] = $item->fileUrl();
            $data['share_url'] = route('library.show', $item);
        }

        return $data;
    }
}
