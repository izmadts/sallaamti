<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishCommunityPostToSocialJob;
use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Models\SocialPostDispatch;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    public function index(Request $request)
    {
        $tag = $request->get('tag');

        $query = CommunityPost::with(['author', 'socialDispatches'])->orderByDesc('is_pinned')->latest();
        if ($tag) {
            $query->withTag($tag);
        }
        $posts = $query->get();

        $tags = CommunityPost::pluck('tags')->flatten()->filter()->unique()->values();

        return view('admin.community-posts.index', ['posts' => $posts, 'tags' => $tags, 'activeTag' => $tag]);
    }

    public function create()
    {
        return view('admin.community-posts.create', ['connectedPlatforms' => $this->connectedPlatforms()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $validated['tags'] = $this->parseTags($request->input('tags'));
        $validated['social_targets'] = $this->parseSocialTargets($request);
        $validated['body'] = HtmlSanitizer::clean($validated['body']);
        $validated['author_id'] = Auth::id();
        $validated['status'] = $request->boolean('publish') ? 'published' : 'draft';
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('community-posts', 'public');
        }
        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('community-posts/videos', 'public');
        }

        $post = CommunityPost::create($validated);

        if ($post->status === 'published') {
            $this->dispatchSocialPosts($post);
        }

        return redirect()->route('admin.community-posts.index')->with('status', 'Post added.');
    }

    public function edit(CommunityPost $community_post)
    {
        return view('admin.community-posts.edit', ['post' => $community_post, 'connectedPlatforms' => $this->connectedPlatforms()]);
    }

    public function update(Request $request, CommunityPost $community_post)
    {
        $wasPublished = $community_post->status === 'published';

        $validated = $this->validated($request);

        $validated['tags'] = $this->parseTags($request->input('tags'));
        $validated['social_targets'] = $this->parseSocialTargets($request);
        $validated['body'] = HtmlSanitizer::clean($validated['body']);
        $validated['status'] = $request->boolean('publish') ? 'published' : 'draft';
        $validated['published_at'] = $validated['status'] === 'published'
            ? ($community_post->published_at ?? now())
            : null;

        if ($request->hasFile('photo')) {
            if ($community_post->photo) Storage::disk('public')->delete($community_post->photo);
            $validated['photo'] = $request->file('photo')->store('community-posts', 'public');
        }
        if ($request->hasFile('video')) {
            if ($community_post->video) Storage::disk('public')->delete($community_post->video);
            $validated['video'] = $request->file('video')->store('community-posts/videos', 'public');
        }

        $community_post->update($validated);

        if (!$wasPublished && $community_post->status === 'published') {
            $this->dispatchSocialPosts($community_post);
        }

        return redirect()->route('admin.community-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(CommunityPost $community_post)
    {
        if ($community_post->photo) Storage::disk('public')->delete($community_post->photo);
        if ($community_post->video) Storage::disk('public')->delete($community_post->video);
        $community_post->delete();
        return back()->with('status', 'Post deleted.');
    }

    public function toggle(CommunityPost $community_post)
    {
        $wasPublished = $community_post->status === 'published';

        $community_post->update([
            'status' => $wasPublished ? 'draft' : 'published',
            'published_at' => $wasPublished ? null : ($community_post->published_at ?? now()),
        ]);

        if (!$wasPublished) {
            $this->dispatchSocialPosts($community_post);
        }

        return back()->with('status', 'Updated.');
    }

    public function togglePin(CommunityPost $community_post)
    {
        $pinned = $community_post->is_pinned;

        $community_post->update([
            'is_pinned' => !$pinned,
            'pinned_at' => $pinned ? null : now(),
        ]);

        return back()->with('status', $pinned ? 'Unpinned.' : 'Pinned to the top of the Wall.');
    }

    // Queues a delivery per selected platform that's actually connected —
    // platforms picked in the form but not (or no longer) connected are
    // silently skipped rather than failing the whole save.
    private function dispatchSocialPosts(CommunityPost $post): void
    {
        foreach ($post->social_targets ?? [] as $platform) {
            $account = SocialAccount::active($platform);
            if (!$account) {
                continue;
            }

            $dispatch = SocialPostDispatch::create([
                'community_post_id' => $post->id,
                'platform' => $platform,
                'social_account_id' => $account->id,
                'status' => 'queued',
            ]);

            PublishCommunityPostToSocialJob::dispatch($dispatch);
        }
    }

    private function connectedPlatforms(): array
    {
        return SocialAccount::where('status', 'connected')->pluck('platform')->all();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
            'event_at' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'video' => ['nullable', 'mimes:mp4,mov,webm', 'max:51200'],
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

    private function parseSocialTargets(Request $request): array
    {
        return collect($request->input('social_targets', []))
            ->filter(fn ($platform) => in_array($platform, SocialAccount::PLATFORMS, true))
            ->values()
            ->all();
    }
}
