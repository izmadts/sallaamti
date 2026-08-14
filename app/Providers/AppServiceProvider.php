<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\BlogPost;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Fix for shared hosting MySQL key length limit
        Schema::defaultStringLength(191);

        // db.* translation keys are the verbatim English string (see
        // App\Models\Translation) — so an untranslated string (including all
        // of English itself, which is never seeded) should fall back to that
        // verbatim text instead of showing the raw "db.Some String" key.
        Lang::handleMissingKeysUsing(function (string $key) {
            return str_starts_with($key, 'db.') ? substr($key, 3) : $key;
        });

        // Share latest 2 blog posts with the shared footer partial (used by both
        // the guest layout and the authenticated app layout)
        View::composer('partials.footer', function ($view) {
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

        // Shares the current user's Nikah-profile completion percentage with
        // the banner component, dropped into layouts.app once, globally —
        // same pattern as the footer composer above. Only Nikah users (i.e.
        // those who have started a Nikah profile) ever see this nudge; it's
        // null for everyone else so the banner stays hidden.
        View::composer('components.profile-completion-banner', function ($view) {
            $nikahProfile = auth()->check() ? auth()->user()->nikahProfile : null;

            $view->with('profileCompletionPercent', $nikahProfile?->completenessPercentage());
        });
    }

    public function register(): void
    {
        //
    }
}