<?php

namespace App\Jobs;

use App\Models\CommunityPost;
use App\Models\User;
use App\Notifications\CommunityAnnouncementPosted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Mandatory in-app notification for every member when admin marks a
// Community Post as an announcement — unlike the WhatsApp broadcast
// (opt-in, admin has to turn it on), this one is unconditional per the
// product requirement: "if it's an announcement, the notification must go
// out." Dispatched from CommunityPostPublisher alongside the social/
// WhatsApp fan-out, only when CommunityPost::is_announcement is true.
class NotifyMembersOfAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly CommunityPost $post)
    {
    }

    public function handle(): void
    {
        // Chunked rather than loaded in one go — this is every app member,
        // not a filtered opt-in subset like the WhatsApp broadcast, so it
        // could be the whole user base.
        User::role('member')->chunkById(200, function ($members) {
            foreach ($members as $member) {
                // One member's notification failing (bad push token, etc.)
                // must not stop the rest of the broadcast.
                try {
                    $member->notify(new CommunityAnnouncementPosted($this->post));
                } catch (\Throwable $e) {
                    \Log::warning("NotifyMembersOfAnnouncementJob: notify user #{$member->id} failed — " . $e->getMessage());
                }
            }
        });
    }
}
