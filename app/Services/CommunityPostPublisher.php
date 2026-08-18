<?php

namespace App\Services;

use App\Jobs\PublishCommunityPostToSocialJob;
use App\Jobs\SendWhatsappBroadcastJob;
use App\Models\CommunityPost;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\SocialPostDispatch;

class CommunityPostPublisher
{
    // Flips a draft/scheduled post live and fans it out to social — the one
    // entry point used by both the admin "Post Now" action and the daily
    // scheduled-batch command, so a queued post goes out exactly the same
    // way a manually-published one does.
    public function publish(CommunityPost $post): void
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
            'queue_position' => null,
        ]);

        $this->dispatchSocialPosts($post);
    }

    // Queues a delivery per selected platform that's actually connected —
    // platforms picked in the form but not (or no longer) connected are
    // silently skipped rather than failing the whole save. This is the one
    // method every "a post just went live" path already calls directly
    // (CommunityPostController's store/update/toggle, plus publish() above
    // for the queue/scheduled-batch path) — so the WhatsApp broadcast below
    // lives here too, rather than in publish(), to fire exactly once
    // regardless of which of those paths triggered it.
    public function dispatchSocialPosts(CommunityPost $post): void
    {
        foreach ($post->social_targets ?? [] as $platform) {
            $account = SocialAccount::active($platform);
            if (!$account) {
                continue;
            }

            $dispatch = SocialPostDispatch::create([
                'community_post_id' => $post->id,
                'platform' => $platform,
                'social_account_id' => $account->id,
                'status' => 'queued',
            ]);

            PublishCommunityPostToSocialJob::dispatch($dispatch);
        }

        $this->dispatchWhatsappBroadcast($post);
    }

    // No-op unless an admin has both connected a WhatsApp Business account
    // AND explicitly flipped whatsapp_notifications_enabled on — both off
    // by default, so this ships inert until turned on deliberately.
    private function dispatchWhatsappBroadcast(CommunityPost $post): void
    {
        if (Setting::get('whatsapp_notifications_enabled', '0') !== '1') {
            return;
        }

        if (!SocialAccount::active('whatsapp')) {
            return;
        }

        SendWhatsappBroadcastJob::dispatch($post);
    }
}
