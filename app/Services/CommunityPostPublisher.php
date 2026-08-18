<?php

namespace App\Services;

use App\Jobs\PublishCommunityPostToSocialJob;
use App\Models\CommunityPost;
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
    // silently skipped rather than failing the whole save.
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
    }
}
