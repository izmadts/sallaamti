<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Services\CommunityPostPublisher;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommunityPostController extends Controller
{
    public function __construct(private CommunityPostPublisher $publisher)
    {
    }

    public function index(Request $request)
    {
        $tag = $request->get('tag');

        $query = CommunityPost::with(['author', 'socialDispatches'])->orderByDesc('is_pinned')->latest();
        if ($tag) {
            $query->withTag($tag);
        }
        $posts = $query->get();

        $tags = CommunityPost::pluck('tags')->flatten()->filter()->unique()->values();
        $scheduledCount = CommunityPost::where('status', 'scheduled')->count();

        return view('admin.community-posts.index', ['posts' => $posts, 'tags' => $tags, 'activeTag' => $tag, 'scheduledCount' => $scheduledCount]);
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
            $this->publisher->dispatchSocialPosts($post);
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
        $isScheduled = $community_post->status === 'scheduled';

        $validated = $this->validated($request);

        $validated['tags'] = $this->parseTags($request->input('tags'));
        $validated['social_targets'] = $this->parseSocialTargets($request);
        $validated['body'] = HtmlSanitizer::clean($validated['body']);

        // A scheduled post's status/queue position is only ever changed by
        // the dedicated Queue actions (Post Now / reorder / delete) — a
        // normal caption edit shouldn't silently knock it out of the queue.
        if (!$isScheduled) {
            $validated['status'] = $request->boolean('publish') ? 'published' : 'draft';
            $validated['published_at'] = $validated['status'] === 'published'
                ? ($community_post->published_at ?? now())
                : null;
        }

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
            $this->publisher->dispatchSocialPosts($community_post);
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
        abort_if($community_post->status === 'scheduled', 422, 'Use the Queue page to publish a scheduled post.');

        $wasPublished = $community_post->status === 'published';

        $community_post->update([
            'status' => $wasPublished ? 'draft' : 'published',
            'published_at' => $wasPublished ? null : ($community_post->published_at ?? now()),
        ]);

        if (!$wasPublished) {
            $this->publisher->dispatchSocialPosts($community_post);
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

    // ===== Bulk upload — old photos/videos become scheduled posts =====

    public function bulkUpload()
    {
        return view('admin.community-posts.bulk-upload', ['connectedPlatforms' => $this->connectedPlatforms()]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:30'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:51200'],
            'tags' => ['nullable', 'string'],
        ]);

        $tags = $this->parseTags($validated['tags'] ?? null);
        $socialTargets = $this->parseSocialTargets($request);
        $nextPosition = (int) (CommunityPost::where('status', 'scheduled')->max('queue_position') ?? -1) + 1;

        foreach ($request->file('files') as $file) {
            $isVideo = str_starts_with($file->getMimeType(), 'video/');

            $post = CommunityPost::create([
                'author_id' => Auth::id(),
                'title' => Str::of($file->getClientOriginalName())->beforeLast('.')->replace(['-', '_'], ' ')->title()->limit(150),
                'body' => '',
                'tags' => $tags,
                'social_targets' => $socialTargets,
                'status' => 'scheduled',
                'queue_position' => $nextPosition++,
            ]);

            if ($isVideo) {
                $post->update(['video' => $file->store('community-posts/videos', 'public')]);
            } else {
                $post->update(['photo' => $file->store('community-posts', 'public')]);
            }
        }

        $count = count($request->file('files'));

        return redirect()->route('admin.community-posts.queue')->with('status', "{$count} " . Str::plural('item', $count) . ' added to the queue — edit captions before they go out.');
    }

    // ===== Queue — drag-and-drop ordering + manual "post now" =====

    public function queue()
    {
        $posts = CommunityPost::scheduled()->get();
        $batchSize = (int) Setting::get('scheduled_batch_size', 3);
        $batchTime = Setting::get('scheduled_batch_time', '09:00');

        return view('admin.community-posts.queue', compact('posts', 'batchSize', 'batchTime'));
    }

    public function queueReorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:community_posts,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['ids'] as $position => $id) {
                CommunityPost::where('id', $id)->where('status', 'scheduled')->update(['queue_position' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function queuePublishNow(CommunityPost $community_post)
    {
        abort_unless($community_post->status === 'scheduled', 404);

        $this->publisher->publish($community_post);

        return back()->with('status', 'Published now.');
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
