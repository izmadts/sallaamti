<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Post;
use App\Models\QuranLiveCourse;
use App\Models\User;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/wall', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => '/team', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/testimonial', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/contact', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => '/courses', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/skills', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/quran-live', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/donate', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/volunteer', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push([
                'loc' => url($page['loc']),
                'lastmod' => now()->toDateString(),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ]);
        }

        Course::where('is_published', true)->get(['slug', 'updated_at'])->each(function ($course) use ($urls) {
            $urls->push([
                'loc' => url('/courses/' . $course->slug),
                'lastmod' => $course->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);
        });

        QuranLiveCourse::where('is_published', true)->get(['id', 'updated_at'])->each(function ($course) use ($urls) {
            $urls->push([
                'loc' => url('/quran-live/' . $course->id),
                'lastmod' => $course->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);
        });

        BlogPost::published()->get(['slug', 'updated_at'])->each(function ($post) use ($urls) {
            $urls->push([
                'loc' => url('/blog/' . $post->slug),
                'lastmod' => $post->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);
        });

        Post::published()->get(['slug', 'updated_at'])->each(function ($post) use ($urls) {
            $urls->push([
                'loc' => url('/posts/' . $post->slug),
                'lastmod' => $post->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ]);
        });

        // One public author page per user with at least one published post
        // — each is a real, distinct indexable URL (name/short bio/their
        // posts), not a duplicate of the posts themselves.
        User::whereNotNull('username')
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->get(['username', 'updated_at'])
            ->each(function ($user) use ($urls) {
                $urls->push([
                    'loc' => url('/posts/author/' . $user->username),
                    'lastmod' => $user->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.3',
                ]);
            });

        // Nikah profiles are deliberately NOT in the sitemap: they show a member's age,
        // city and marital status, so they must not be searchable in Google (privacy).
        // The profile page itself also sends noindex (see NikahProfileController::publicView).

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
