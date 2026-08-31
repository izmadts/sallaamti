<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommunityPost;
use App\Models\DuaRequest;
use App\Models\Reaction;
use App\Models\SavedPost;
use App\Notifications\CommentReceived;
use App\Notifications\ReactionReceived;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// The mobile-app side of WallController (public web feed). Same merged
// DuaRequest + CommunityPost feed, reactions, comments, and saves — just
// JSON instead of Blade partials, and {type}/{id} pairs instead of two
// parallel route sets per action (dua vs post) like the web version has.
class WallController extends Controller
{
    private function findItem(string $type, int $id): Model
    {
        return match ($type) {
            'dua' => DuaRequest::where('status', 'approved')->findOrFail($id),
            'post' => CommunityPost::published()->findOrFail($id),
            default => abort(404),
        };
    }

    private function itemPayload(Model $item, ?int $userId): array
    {
        $isDua = $item instanceof DuaRequest;
        $author = $isDua ? $item->user : $item->author;
        $hideAuthor = $isDua && $item->is_anonymous;

        $reactionCounts = array_fill_keys(array_keys(Reaction::TYPES), 0);
        foreach ($item->reactions as $reaction) {
            if (array_key_exists($reaction->type, $reactionCounts)) {
                $reactionCounts[$reaction->type]++;
            }
        }
        $myReaction = $userId ? $item->reactions->firstWhere('user_id', $userId)?->type : null;
        $isSaved = $userId ? $item->savedPosts->contains('user_id', $userId) : false;

        return [
            'type' => $isDua ? 'dua' : 'post',
            'id' => $item->id,
            'author' => ($hideAuthor || !$author) ? null : ['name' => $author->name, 'avatar' => $author->apiAvatarUrl()],
            'is_anonymous' => $isDua ? (bool) $item->is_anonymous : false,
            'title' => $isDua ? null : $item->title,
            'body' => $item->body,
            'photo_url' => (!$isDua && $item->photo) ? Storage::disk('public')->url($item->photo) : null,
            'video_url' => (!$isDua && $item->video) ? Storage::disk('public')->url($item->video) : null,
            'tags' => $isDua ? [] : ($item->tags ?? []),
            'event_at' => $isDua ? null : $item->event_at,
            'is_pinned' => $isDua ? false : (bool) $item->is_pinned,
            'reaction_counts' => $reactionCounts,
            'reaction_types' => collect(Reaction::TYPES)->map(fn ($t) => ['emoji' => $t[0], 'label' => $t[1]])->all(),
            'my_reaction' => $myReaction,
            'comments_count' => (int) ($item->comments_count ?? $item->allComments()->count()),
            'is_saved' => $isSaved,
            'created_at' => $item->created_at,
        ];
    }

    private function commentPayload(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'author' => $comment->user ? ['name' => $comment->user->name, 'avatar' => $comment->user->apiAvatarUrl()] : null,
            'body' => $comment->body,
            'parent_id' => $comment->parent_id,
            'created_at' => $comment->created_at,
            'replies' => $comment->relationLoaded('replies') ? $comment->replies->map(fn ($r) => $this->commentPayload($r))->all() : [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $tag = $request->get('tag');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 10;
        $userId = $request->user()?->id;

        if ($tag && $tag !== 'all' && $tag !== 'dua') {
            $paginated = CommunityPost::published()->withTag($tag)
                ->with(['author', 'reactions', 'savedPosts'])
                ->withCount('allComments as comments_count')
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } elseif ($tag === 'dua') {
            $paginated = DuaRequest::where('status', 'approved')
                ->with(['user', 'reactions', 'savedPosts'])
                ->withCount('allComments as comments_count')
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $duas = DuaRequest::where('status', 'approved')
                ->with(['user', 'reactions', 'savedPosts'])
                ->withCount('allComments as comments_count')
                ->latest()
                ->limit(300)
                ->get();

            $posts = CommunityPost::published()
                ->with(['author', 'reactions', 'savedPosts'])
                ->withCount('allComments as comments_count')
                ->latest()
                ->limit(300)
                ->get();

            $merged = $duas->concat($posts)->sortBy([
                fn ($a, $b) => (($b->is_pinned ?? false) <=> ($a->is_pinned ?? false)),
                fn ($a, $b) => ($b->created_at <=> $a->created_at),
            ])->values();

            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $merged->forPage($page, $perPage),
                $merged->count(),
                $perPage,
                $page,
            );
        }

        $tags = CommunityPost::published()->pluck('tags')->flatten()->filter()->unique()->values();

        return response()->json([
            'items' => $paginated->getCollection()->map(fn (Model $item) => $this->itemPayload($item, $userId))->values(),
            'has_more' => $paginated->hasMorePages(),
            'tags' => $tags,
        ]);
    }

    public function saved(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->get('page', 1));
        $userId = $request->user()->id;

        $paginated = SavedPost::where('user_id', $userId)
            ->with(['saveable' => function ($morphTo) {
                $morphTo->morphWith([
                    DuaRequest::class => ['user', 'reactions', 'savedPosts'],
                    CommunityPost::class => ['author', 'reactions', 'savedPosts'],
                ]);
            }])
            ->latest()
            ->paginate(10, ['*'], 'page', $page);

        $items = $paginated->getCollection()
            ->map(fn (SavedPost $sp) => $sp->saveable ? tap($sp->saveable, fn ($m) => $m->loadCount('allComments as comments_count')) : null)
            ->filter()
            ->values();

        return response()->json([
            'items' => $items->map(fn (Model $item) => $this->itemPayload($item, $userId))->values(),
            'has_more' => $paginated->hasMorePages(),
        ]);
    }

    public function storeDua(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        DuaRequest::create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Your dua has been submitted and will appear once our team reviews it.'], 201);
    }

    public function react(string $type, int $id, Request $request): JsonResponse
    {
        $item = $this->findItem($type, $id);
        $reactionType = $request->input('type', 'ameen');
        abort_unless(array_key_exists($reactionType, Reaction::TYPES), 422, 'Invalid reaction type.');

        $user = $request->user();
        $existing = Reaction::where('reactable_type', get_class($item))
            ->where('reactable_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->type === $reactionType) {
            $existing->delete();
        } else {
            $isNew = !$existing;
            if ($existing) {
                $existing->update(['type' => $reactionType]);
            } else {
                Reaction::create([
                    'reactable_type' => get_class($item),
                    'reactable_id' => $item->id,
                    'user_id' => $user->id,
                    'type' => $reactionType,
                ]);
            }

            $authorId = $item instanceof DuaRequest ? $item->user_id : $item->author_id;
            if ($isNew && $authorId && $authorId !== $user->id) {
                try {
                    $author = $item instanceof DuaRequest ? $item->user : $item->author;
                    $author?->notify(new ReactionReceived($item, $user, $reactionType));
                } catch (\Throwable $e) {
                    \Log::error('ReactionReceived notification failed: ' . $e->getMessage());
                }
            }
        }

        $item->load('reactions', 'savedPosts');

        return response()->json($this->itemPayload($item, $user->id));
    }

    public function save(string $type, int $id, Request $request): JsonResponse
    {
        $item = $this->findItem($type, $id);
        $user = $request->user();

        $existing = SavedPost::where('saveable_type', get_class($item))
            ->where('saveable_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            SavedPost::create(['saveable_type' => get_class($item), 'saveable_id' => $item->id, 'user_id' => $user->id]);
        }

        return response()->json(['is_saved' => !$existing]);
    }

    public function comments(string $type, int $id): JsonResponse
    {
        $item = $this->findItem($type, $id);
        $item->load('comments.replies.user', 'comments.user');

        return response()->json(['comments' => $item->comments->map(fn (Comment $c) => $this->commentPayload($c))->values()]);
    }

    public function storeComment(string $type, int $id, Request $request): JsonResponse
    {
        $item = $this->findItem($type, $id);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = Comment::where('id', $validated['parent_id'])
                ->where('commentable_type', get_class($item))
                ->where('commentable_id', $item->id)
                ->whereNull('parent_id')
                ->first();

            abort_unless($parent, 422, 'Cannot reply to that comment.');
        }

        $user = $request->user();
        $comment = Comment::create([
            'commentable_type' => get_class($item),
            'commentable_id' => $item->id,
            'user_id' => $user->id,
            'parent_id' => $parent?->id,
            'body' => $validated['body'],
        ]);
        $comment->load('user');

        $postAuthor = $item instanceof DuaRequest ? $item->user : $item->author;
        $recipients = collect([$postAuthor, $parent?->user])->filter()->unique('id')->reject(fn ($r) => $r->id === $user->id);
        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new CommentReceived($comment, $user));
            } catch (\Throwable $e) {
                \Log::error('CommentReceived notification failed: ' . $e->getMessage());
            }
        }

        return response()->json(['comment' => $this->commentPayload($comment)], 201);
    }

    public function destroyComment(Comment $comment, Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless((int) $comment->user_id === (int) $user->id || $user->hasRole('admin'), 403);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }
}
