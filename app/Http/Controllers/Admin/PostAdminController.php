<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Notifications\PostReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostAdminController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Post::count(),
            'pending' => Post::where('status', 'pending')->count(),
            'published' => Post::where('status', 'published')->count(),
            'rejected' => Post::where('status', 'rejected')->count(),
        ];

        $status = $request->get('status', 'pending');
        $query = Post::with('author')->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $posts = $query->paginate(20)->withQueryString();

        return view('admin.posts.index', compact('posts', 'stats', 'status'));
    }

    public function approve(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $post->author->notify(new PostReviewed($post, approved: true));

        return back()->with('status', 'Post approved and is now public.');
    }

    public function reject(Request $request, Post $post)
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $post->update([
            'status' => 'rejected',
            'published_at' => null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $post->author->notify(new PostReviewed($post, approved: false));

        return back()->with('status', 'Post rejected.');
    }

    public function destroy(Post $post)
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return back()->with('status', 'Post permanently deleted.');
    }
}
