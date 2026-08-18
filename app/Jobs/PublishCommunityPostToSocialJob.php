<?php

namespace App\Jobs;

use App\Models\SocialPostDispatch;
use App\Services\SocialPublishing\FacebookPublisher;
use App\Services\SocialPublishing\InstagramPublisher;
use App\Services\SocialPublishing\TiktokPublisher;
use App\Services\SocialPublishing\TwitterPublisher;
use App\Services\SocialPublishing\YoutubePublisher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishCommunityPostToSocialJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    private const PUBLISHERS = [
        'facebook' => FacebookPublisher::class,
        'instagram' => InstagramPublisher::class,
        'twitter' => TwitterPublisher::class,
        'youtube' => YoutubePublisher::class,
        'tiktok' => TiktokPublisher::class,
    ];

    public function __construct(private readonly SocialPostDispatch $dispatch)
    {
    }

    public function handle(): void
    {
        $this->dispatch->increment('attempts');

        $account = $this->dispatch->socialAccount;
        $publisherClass = self::PUBLISHERS[$this->dispatch->platform] ?? null;

        if (!$account || !$publisherClass) {
            $this->dispatch->update(['status' => 'failed', 'error_message' => 'No connected account for this platform.']);
            return;
        }

        // Deliberately not caught here — a network blip, rate limit, or
        // transient 5xx from the platform's API needs to propagate so
        // Laravel's $tries/$backoff actually retry it. Catching everything
        // on the first attempt (the previous behavior) made that retry
        // config dead code and treated transient and permanent failures
        // identically. failed() below marks the dispatch once retries are
        // truly exhausted.
        $result = app($publisherClass)->publish($this->dispatch->communityPost, $account);

        if ($result['success']) {
            $this->dispatch->update([
                'status' => 'sent',
                'external_post_id' => $result['external_id'],
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } else {
            // A structured failure the publisher itself reported (bad
            // content, rejected media, expired token) — retrying the same
            // request won't change the outcome, so this is terminal
            // immediately rather than burning through $tries.
            \Log::error("PublishCommunityPostToSocialJob: {$this->dispatch->platform} publish failed — {$result['error']}");
            $this->dispatch->update(['status' => 'failed', 'error_message' => $result['error']]);
        }
    }

    // Called once by the queue worker after $tries genuine exceptions (or
    // any other failure mode Laravel itself detects, e.g. a timeout) —
    // without this, a dispatch that fails this way was left at whatever
    // status it already had ('queued'), invisible to the admin retry UI.
    public function failed(\Throwable $e): void
    {
        \Log::error("PublishCommunityPostToSocialJob: {$this->dispatch->platform} publish failed permanently — " . $e->getMessage());
        $this->dispatch->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
    }
}
