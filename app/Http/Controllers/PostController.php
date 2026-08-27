<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Guest-readable — every published post is a standalone, shareable,
    // indexable page (the whole point: more real public URLs for SEO, each
    // one with its own title/excerpt/author byline for link previews).
    public function index(Request $request)
    {
        $posts = Post::published()->with('author')->latest('published_at')->paginate(12);

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        $viewer = Auth::user();
        $canPreview = $viewer && ($viewer->id === $post->user_id || $viewer->can('posts.manage'));

        abort_unless($post->status === 'published' || $canPreview, 404);

        // Only a real public view increments the counter — the author/staff
        // previewing a pending post shouldn't inflate it before it's live.
        if ($post->status === 'published' && !$canPreview) {
            $post->increment('views_count');
        }

        $morePosts = Post::published()
            ->where('user_id', $post->user_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('posts.show', compact('post', 'morePosts'));
    }

    // A lightweight public "author page" — doubles as the user's public
    // profile (name, avatar, short bio) and as another indexable URL per
    // active author, exactly the kind of thing that grows the site's real
    // public URL count for SEO rather than just static marketing pages.
    public function byAuthor(User $user)
    {
        abort_unless($user->username, 404);

        $posts = Post::published()->where('user_id', $user->id)->latest('published_at')->paginate(12);

        return view('posts.by-author', compact('user', 'posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        Auth::user()->ensureUsername();

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['excerpt'] = ($validated['excerpt'] ?? null) ?: Str::limit(strip_tags($validated['body']), 160);

        if (Post::isAutoPublishedFor(Auth::user())) {
            $validated['status'] = 'published';
            $validated['published_at'] = now();
            $validated['reviewed_by'] = Auth::id();
            $validated['reviewed_at'] = now();
        } else {
            $validated['status'] = 'pending';
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        $post = Post::create($validated);

        $status = $post->status === 'published'
            ? 'Your post is live! Share the link below.'
            : "Thanks — your post was submitted and will appear once our team reviews it.";

        return redirect()->route('posts.mine')->with('status', $status);
    }

    // A member's own submissions across every status — the only place a
    // pending/rejected post is visible to anyone but staff.
    public function mine()
    {
        $posts = Post::where('user_id', Auth::id())->latest()->paginate(15);

        return view('posts.mine', compact('posts'));
    }

    public function edit(Post $post)
    {
        $this->authorizeEdit($post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizeEdit($post);

        $validated = $this->validatePost($request);
        $validated['excerpt'] = ($validated['excerpt'] ?? null) ?: Str::limit(strip_tags($validated['body']), 160);

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        // An already-published post edited by its non-privileged author goes
        // back to pending — otherwise the approval gate is pointless (get
        // one harmless post approved, then silently rewrite it into
        // anything). Staff/admin edits are trusted and don't reset status.
        if ($post->status === 'published' && !Post::isAutoPublishedFor(Auth::user())) {
            $validated['status'] = 'pending';
            $validated['published_at'] = null;
        }

        $post->update($validated);

        return redirect()->route('posts.mine')->with('status', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        abort_unless(Auth::id() === $post->user_id || Auth::user()->can('posts.delete'), 403);

        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return back()->with('status', 'Post deleted.');
    }

    private function authorizeEdit(Post $post): void
    {
        abort_unless(Auth::id() === $post->user_id || Auth::user()->can('posts.manage'), 403);
    }

    private function validatePost(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        // Trix's UI only controls what a well-behaved browser sends — a
        // direct POST can still carry raw <script>/onerror payloads, so the
        // real boundary is here, not the editor. Same sanitizer already
        // guarding blog/lesson/community-post content elsewhere.
        $validated['body'] = HtmlSanitizer::clean($validated['body']);

        return $validated;
    }
}
