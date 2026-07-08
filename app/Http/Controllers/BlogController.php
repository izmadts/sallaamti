<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()->with('author')->latest('published_at')->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $blog_post)
    {
        abort_unless($blog_post->status === 'published', 404);

        return view('blog.show', ['post' => $blog_post]);
    }
}
