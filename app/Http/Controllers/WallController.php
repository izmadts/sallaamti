<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\DuaRequest;
use App\Models\Reaction;
use App\Notifications\ReactionReceived;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WallController extends Controller
{
    // Guest-readable (a good top-of-funnel page — "see what our community is
    // praying for / doing"), but posting/reacting requires login, same as
    // every other social action on the site.
    public function index(Request $request)
    {
        $tag = $request->get('tag');
        $page = $request->get('page', 1);
        $perPage = 10;

        if ($tag && $tag !== 'all' && $tag !== 'dua') {
            // A real CommunityPost tag (Activity/Event/Sermon/anything an
            // admin has used) — single-model query, can paginate directly.
            $items = CommunityPost::published()->withTag($tag)
                ->with(['author', 'reactions'])
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } elseif ($tag === 'dua') {
            $items = DuaRequest::where('status', 'approved')
                ->with(['user', 'reactions'])
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            // No filter — merge both content types. Capped at 300 each side
            // rather than an unbounded ->get(), since this now covers two
            // growing tables instead of one.
            $duas = DuaRequest::where('status', 'approved')
                ->with(['user', 'reactions'])
                ->latest()
                ->limit(300)
                ->get();

            $posts = CommunityPost::published()
                ->with(['author', 'reactions'])
                ->latest()
                ->limit(300)
                ->get();

            $merged = $duas->concat($posts)->sortByDesc('created_at')->values();

            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $merged->forPage($page, $perPage),
                $merged->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        $tags = CommunityPost::published()->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dua-wall.partials.feed-items', ['items' => $items])->render(),
                'has_more' => $items->hasMorePages(),
            ]);
        }

        return view('dua-wall.index', ['paginated' => $items, 'tags' => $tags, 'activeTag' => $tag ?: 'all']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        DuaRequest::create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
        ]);

        return back()->with('status', "Your dua has been submitted and will appear once our team reviews it. JazakAllah Khair for sharing.");
    }

    public function react(DuaRequest $duaRequest, Request $request)
    {
        abort_unless($duaRequest->status === 'approved', 404);

        return response()->json($this->toggleReaction($duaRequest, $request));
    }

    public function postReact(CommunityPost $communityPost, Request $request)
    {
        abort_unless($communityPost->status === 'published', 404);

        return response()->json($this->toggleReaction($communityPost, $request));
    }

    private function toggleReaction(Model $reactable, Request $request): array
    {
        $type = $request->input('type', 'ameen');
        abort_unless(array_key_exists($type, Reaction::TYPES), 422);

        $existing = Reaction::where('reactable_type', get_class($reactable))
            ->where('reactable_id', $reactable->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->type === $type) {
            // Tapping the same reaction again removes it.
            $existing->delete();
            $reacted = false;
            $activeType = null;
        } else {
            $isNew = !$existing;

            if ($existing) {
                $existing->update(['type' => $type]);
            } else {
                Reaction::create([
                    'reactable_type' => get_class($reactable),
                    'reactable_id' => $reactable->id,
                    'user_id' => Auth::id(),
                    'type' => $type,
                ]);
            }

            $reacted = true;
            $activeType = $type;

            $authorId = $reactable instanceof DuaRequest ? $reactable->user_id : $reactable->author_id;

            if ($isNew && $authorId && $authorId !== Auth::id()) {
                try {
                    $author = $reactable instanceof DuaRequest ? $reactable->user : $reactable->author;
                    $author?->notify(new ReactionReceived($reactable, Auth::user(), $type));
                } catch (\Throwable $e) {
                    \Log::error('ReactionReceived notification failed: ' . $e->getMessage());
                }
            }
        }

        return [
            'reacted' => $reacted,
            'type' => $activeType,
            'count' => $reactable->reactions()->count(),
        ];
    }
}
