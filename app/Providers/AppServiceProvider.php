<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\BlogPost;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Fix for shared hosting MySQL key length limit
        Schema::defaultStringLength(191);
        // Share latest 2 blog posts with ALL guest layout views
        View::composer('layouts.guest', function ($view) {
            try {
                $footerPosts = \App\Models\BlogPost::where('status', 'published')
                    ->orderByDesc('published_at')
                    ->take(2)
                    ->get();
            } catch (\Exception $e) {
                $footerPosts = collect();
            }

            $view->with('footerPosts', $footerPosts);
        });
    }

    public function register(): void
    {
        //
    }
}