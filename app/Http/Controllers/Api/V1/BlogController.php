<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\PaginatesApiResponses;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Read-only mirror of the web's BlogController. The blog is staff-authored
// (admin panel), so there's nothing for a member to submit here — this just
// brings the reading side into the app.
class BlogController extends Controller
{
    use PaginatesApiResponses;

    private function payload(BlogPost $post, bool $withContent = false): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'excerpt' => $post->excerpt,
            'cover_image_url' => $post->cover_image ? Storage::url($post->cover_image) : null,
            'published_at' => $post->published_at?->toIso8601String(),
            'author' => $post->author ? ['id' => $post->author->id, 'name' => $post->author->name] : null,
            ...$withContent ? ['content' => $post->content] : [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $posts = BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->paginate(10);

        return response()->json($this->paginated($posts, 'posts', fn (BlogPost $p) => $this->payload($p)));
    }

    public function show(BlogPost $blogPost): JsonResponse
    {
        abort_unless($blogPost->status === 'published', 404);

        $blogPost->load('author');

        return response()->json(['post' => $this->payload($blogPost, withContent: true)]);
    }
}
