<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $posts = BlogPost::with('author')
            ->when(!$user->hasAnyRole(['admin', 'manager']), fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(10);

        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog-posts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'draft';

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('blog-posts', 'public');
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post created.');
    }

    public function edit(BlogPost $blog_post)
    {
        $this->authorizePost($blog_post);

        return view('admin.blog-posts.edit', ['post' => $blog_post]);
    }

    public function update(Request $request, BlogPost $blog_post)
    {
        $this->authorizePost($blog_post);

        $validated = $this->validatePost($request);

        if ($request->hasFile('cover_image')) {
            if ($blog_post->cover_image) {
                Storage::disk('public')->delete($blog_post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('blog-posts', 'public');
        }

        $blog_post->update($validated);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $blog_post)
    {
        $this->authorizePost($blog_post);

        if ($blog_post->cover_image) {
            Storage::disk('public')->delete($blog_post->cover_image);
        }

        $blog_post->delete();

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post deleted.');
    }

    public function publish(BlogPost $blog_post)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'manager']), 403);

        $blog_post->update(['status' => 'published', 'published_at' => now()]);

        return back()->with('status', 'Post published.');
    }

    public function unpublish(BlogPost $blog_post)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'manager']), 403);

        $blog_post->update(['status' => 'draft', 'published_at' => null]);

        return back()->with('status', 'Post unpublished.');
    }

    private function authorizePost(BlogPost $blogPost): void
    {
        abort_unless(
            (int) $blogPost->user_id === (int) Auth::id() || Auth::user()->hasAnyRole(['admin', 'manager']),
            403
        );
    }

    private function validatePost(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['content'] = HtmlSanitizer::clean($validated['content']);

        return $validated;
    }
}
