<?php

namespace App\Services\SocialPublishing\Concerns;

use App\Models\CommunityPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FormatsPostContent
{
    protected function caption(CommunityPost $post, int $maxLength = 2000): string
    {
        $text = trim($post->title . "\n\n" . strip_tags($post->body));

        return Str::limit($text, $maxLength);
    }

    protected function photoUrl(CommunityPost $post): ?string
    {
        return $post->photo ? Storage::disk('public')->url($post->photo) : null;
    }

    protected function videoUrl(CommunityPost $post): ?string
    {
        return $post->video ? Storage::disk('public')->url($post->video) : null;
    }

    protected function videoPath(CommunityPost $post): ?string
    {
        return $post->video ? Storage::disk('public')->path($post->video) : null;
    }
}
