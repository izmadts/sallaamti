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

        if ($hashtags = $this->hashtagsLine($post)) {
            $text .= "\n\n" . $hashtags;
        }

        // Appended before truncating, not after — the whole point of a
        // per-platform $maxLength (280 for X, 500 for Threads, etc.) is to
        // never exceed that platform's real limit, and hashtags tacked on
        // afterward would silently blow past it.
        //
        // Str::limit()'s default $end is '...' (3 chars) APPENDED after
        // truncating to $maxLength — so the returned string was actually up
        // to $maxLength + 3 characters, silently exceeding platforms' real
        // hard limits (X rejecting a 283-char tweet against a 280 limit,
        // for example). Passing '' as $end truncates to exactly $maxLength.
        return Str::limit($text, $maxLength, '');
    }

    // Admin types hashtags/keywords free-form in one field (space- or
    // comma-separated, with or without a leading '#') — this normalizes
    // them into "#One #Two #Three" text for platforms that show hashtags
    // inline in the caption/description. Deliberately doesn't cap *how
    // many* the admin can enter (that's their call, shown as guidance in
    // the form's tooltip, not enforced here) — the per-platform caption()
    // length limit above still protects against exceeding a real API limit.
    protected function hashtagsLine(CommunityPost $post): ?string
    {
        $tags = $this->splitHashtagsField($post);

        return $tags->isEmpty() ? null : $tags->map(fn ($tag) => '#' . $tag)->implode(' ');
    }

    // YouTube's keyword tags are a separate structured metadata field, not
    // hashtag text — same source field as hashtagsLine(), just without the
    // '#' prefix. YouTube hard-rejects the whole upload if the tags'
    // combined length exceeds 500 characters, so this actually enforces
    // that limit (not just a recommendation) by dropping tags once adding
    // another would cross it, rather than failing the video entirely.
    protected function keywordTags(CommunityPost $post, int $maxCombinedLength = 500): array
    {
        $tags = [];
        $length = 0;

        foreach ($this->splitHashtagsField($post) as $tag) {
            $length += strlen($tag) + 1; // +1 for YouTube's internal comma-join
            if ($length > $maxCombinedLength) {
                break;
            }
            $tags[] = $tag;
        }

        return $tags;
    }

    private function splitHashtagsField(CommunityPost $post): \Illuminate\Support\Collection
    {
        if (!$post->hashtags) {
            return collect();
        }

        return collect(preg_split('/[\s,]+/', trim($post->hashtags)))
            ->filter()
            ->map(fn ($tag) => ltrim($tag, '#'))
            ->filter()
            ->unique()
            ->values();
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
