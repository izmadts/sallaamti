<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    public function index(Request $request)
    {
        $tag = $request->get('tag');

        $query = CommunityPost::with('author')->latest();
        if ($tag) {
            $query->withTag($tag);
        }
        $posts = $query->get();

        $tags = CommunityPost::pluck('tags')->flatten()->filter()->unique()->values();

        return view('admin.community-posts.index', ['posts' => $posts, 'tags' => $tags, 'activeTag' => $tag]);
    }

    public function create()
    {
        return view('admin.community-posts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $validated['tags'] = $this->parseTags($request->input('tags'));
        $validated['body'] = HtmlSanitizer::clean($validated['body']);
        $validated['author_id'] = Auth::id();
        $validated['status'] = $request->boolean('publish') ? 'published' : 'draft';
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('community-posts', 'public');
        }

        CommunityPost::create($validated);

        return redirect()->route('admin.community-posts.index')->with('status', 'Post added.');
    }

    public function edit(CommunityPost $community_post)
    {
        return view('admin.community-posts.edit', ['post' => $community_post]);
    }

    public function update(Request $request, CommunityPost $community_post)
    {
        $validated = $this->validated($request);

        $validated['tags'] = $this->parseTags($request->input('tags'));
        $validated['body'] = HtmlSanitizer::clean($validated['body']);
        $validated['status'] = $request->boolean('publish') ? 'published' : 'draft';
        $validated['published_at'] = $validated['status'] === 'published'
            ? ($community_post->published_at ?? now())
            : null;

        if ($request->hasFile('photo')) {
            if ($community_post->photo) Storage::disk('public')->delete($community_post->photo);
            $validated['photo'] = $request->file('photo')->store('community-posts', 'public');
        }

        $community_post->update($validated);

        return redirect()->route('admin.community-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(CommunityPost $community_post)
    {
        if ($community_post->photo) Storage::disk('public')->delete($community_post->photo);
        $community_post->delete();
        return back()->with('status', 'Post deleted.');
    }

    public function toggle(CommunityPost $community_post)
    {
        $published = $community_post->status === 'published';
        $community_post->update([
            'status' => $published ? 'draft' : 'published',
            'published_at' => $published ? null : ($community_post->published_at ?? now()),
        ]);

        return back()->with('status', 'Updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
            'event_at' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function parseTags(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
