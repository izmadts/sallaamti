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

        try {
            $result = app($publisherClass)->publish($this->dispatch->communityPost, $account);
        } catch (\Throwable $e) {
            \Log::error("PublishCommunityPostToSocialJob failed for {$this->dispatch->platform}: " . $e->getMessage());
            $this->dispatch->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return;
        }

        if ($result['success']) {
            $this->dispatch->update([
                'status' => 'sent',
                'external_post_id' => $result['external_id'],
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } else {
            \Log::error("PublishCommunityPostToSocialJob: {$this->dispatch->platform} publish failed — {$result['error']}");
            $this->dispatch->update(['status' => 'failed', 'error_message' => $result['error']]);
        }
    }
}
