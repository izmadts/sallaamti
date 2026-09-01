<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\PaginatesApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageOptimizer;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// The mobile side of the web's PostController — member-authored posts that
// go live after review. Same approval gate, same "editing a published post
// sends it back to pending" rule; without that, the gate would be trivially
// bypassable by getting one harmless post approved and rewriting it.
//
// Deliberately not the same thing as the Wall (WallController), which is the
// dua/community-post feed. A Post is a long-form, publicly shareable article
// with its own URL.
class PostController extends Controller
{
    use PaginatesApiResponses;

    private function payload(Post $post, bool $withBody = false): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'cover_image_url' => $post->cover_image ? Storage::url($post->cover_image) : null,
            'status' => $post->status,
            'rejection_reason' => $post->rejection_reason,
            'views_count' => $post->views_count,
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at?->toIso8601String(),
            'author' => $post->author ? [
                'id' => $post->author->id,
                'name' => $post->author->name,
                'avatar' => $post->author->apiAvatarUrl(),
            ] : null,
            // The public URL, so the app can offer a real "share this" rather
            // than only being readable inside the app.
            'share_url' => $post->status === 'published' ? url('/posts/' . $post->slug) : null,
            ...$withBody ? ['body' => $post->body] : [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $posts = Post::published()->with('author')->latest('published_at')->paginate(10);

        return response()->json($this->paginated($posts, 'posts', fn (Post $p) => $this->payload($p)));
    }

    public function show(Post $post, Request $request): JsonResponse
    {
        $viewer = $request->user();
        $canPreview = (int) $viewer->id === (int) $post->user_id || $viewer->can('posts.manage');

        abort_unless($post->status === 'published' || $canPreview, 404);

        // Only a real public read counts — an author previewing their own
        // pending post shouldn't inflate the number, same as on web.
        if ($post->status === 'published' && !$canPreview) {
            $post->increment('views_count');
        }

        $post->load('author');

        return response()->json(['post' => $this->payload($post, withBody: true)]);
    }

    /** The member's own posts across every status — where a pending or rejected one is visible. */
    public function mine(Request $request): JsonResponse
    {
        $posts = Post::where('user_id', $request->user()->id)->with('author')->latest()->paginate(15);

        return response()->json($this->paginated($posts, 'posts', fn (Post $p) => $this->payload($p)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePost($request);
        $user = $request->user();

        $user->ensureUsername();

        $validated['user_id'] = $user->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['excerpt'] = ($validated['excerpt'] ?? null) ?: Str::limit(strip_tags($validated['body']), 160);

        if (Post::isAutoPublishedFor($user)) {
            $validated['status'] = 'published';
            $validated['published_at'] = now();
            $validated['reviewed_by'] = $user->id;
            $validated['reviewed_at'] = now();
        } else {
            $validated['status'] = 'pending';
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = ImageOptimizer::store($request->file('cover_image'), 'posts', 'public', maxDimension: 1600, quality: 82);
        }

        $post = Post::create($validated);
        $post->load('author');

        return response()->json([
            'post' => $this->payload($post),
            'message' => $post->status === 'published'
                ? __('db.Your post is live! Share the link below.')
                : __('db.Thanks — your post was submitted and will appear once our team reviews it.'),
        ], 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorizeEdit($post, $request);

        $validated = $this->validatePost($request);
        $validated['excerpt'] = ($validated['excerpt'] ?? null) ?: Str::limit(strip_tags($validated['body']), 160);

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = ImageOptimizer::store($request->file('cover_image'), 'posts', 'public', maxDimension: 1600, quality: 82);
        }

        // Same rule as web: a published post edited by its non-privileged
        // author returns to pending, or the approval gate means nothing.
        if ($post->status === 'published' && !Post::isAutoPublishedFor($request->user())) {
            $validated['status'] = 'pending';
            $validated['published_at'] = null;
        }

        $post->update($validated);
        $post->load('author');

        return response()->json(['post' => $this->payload($post), 'message' => __('db.Post updated.')]);
    }

    public function destroy(Post $post, Request $request): JsonResponse
    {
        abort_unless(
            (int) $request->user()->id === (int) $post->user_id || $request->user()->can('posts.delete'),
            403
        );

        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return response()->json(['status' => 'ok', 'message' => __('db.Post deleted.')]);
    }

    private function authorizeEdit(Post $post, Request $request): void
    {
        abort_unless(
            (int) $request->user()->id === (int) $post->user_id || $request->user()->can('posts.manage'),
            403
        );
    }

    private function validatePost(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        // The app composes in plain text where the web uses Trix, so this
        // normalizes either into the same safe HTML — see
        // HtmlSanitizer::cleanAuthoredText. Sanitizing is the real boundary
        // regardless of which client sent it.
        $validated['body'] = HtmlSanitizer::cleanAuthoredText($validated['body']);

        return $validated;
    }
}
