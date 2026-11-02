<?php

namespace App\Jobs;

use App\Models\CommunityPost;
use App\Models\User;
use App\Notifications\CommunityPostPublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Same fan-out as NotifyMembersOfAnnouncementJob, for a regular (non-
// announcement) post — dispatched from CommunityPostPublisher for every
// published Community Post that ISN'T an announcement (an announcement
// already gets its own, separate notification; this would double-notify
// on top of it otherwise).
class NotifyMembersOfNewPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly CommunityPost $post)
    {
    }

    public function handle(): void
    {
        User::role('member')->chunkById(200, function ($members) {
            foreach ($members as $member) {
                try {
                    $member->notify(new CommunityPostPublished($this->post));
                } catch (\Throwable $e) {
                    \Log::warning("NotifyMembersOfNewPostJob: notify user #{$member->id} failed — " . $e->getMessage());
                }
            }
        });
    }
}
