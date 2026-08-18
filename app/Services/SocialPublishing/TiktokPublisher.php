<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class TiktokPublisher implements SocialPublisher
{
    use FormatsPostContent;

    // TikTok's Content Posting API only accepts video — no text/photo post
    // type here, matching what was actually asked for ("reels").
    //
    // Until the admin's TikTok Developer app passes TikTok's own Content
    // Posting API audit, TikTok restricts unaudited apps to SELF_ONLY
    // privacy (the post lands as a private draft in the creator's own
    // inbox to finish manually) — that's a TikTok platform policy, not
    // something this code can route around. Flip `tiktok_audited` to '1'
    // in Settings once TikTok approves the app to switch to public posts.
    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $videoUrl = $this->videoUrl($post);

        if (!$videoUrl) {
            return ['success' => false, 'external_id' => null, 'error' => 'TikTok requires a video — this post has none.'];
        }

        $audited = Setting::get('tiktok_audited', '0') === '1';

        $init = Http::withToken($account->access_token)->post('https://open.tiktokapis.com/v2/post/publish/video/init/', [
            'post_info' => [
                'title' => $this->caption($post, 150),
                'privacy_level' => $audited ? 'PUBLIC_TO_EVERYONE' : 'SELF_ONLY',
            ],
            'source_info' => [
                'source' => 'PULL_FROM_URL',
                'video_url' => $videoUrl,
            ],
        ]);

        if ($init->failed() || $init->json('error.code') !== 'ok') {
            return ['success' => false, 'external_id' => null, 'error' => $init->json('error.message') ?? ('HTTP ' . $init->status())];
        }

        $publishId = $init->json('data.publish_id');
        $result = $this->waitUntilComplete($publishId, $account->access_token);

        if (!$result['success']) {
            return ['success' => false, 'external_id' => null, 'error' => $result['error']];
        }

        return ['success' => true, 'external_id' => $publishId, 'error' => null];
    }

    private function waitUntilComplete(string $publishId, string $token, int $maxAttempts = 15): array
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $status = Http::withToken($token)->post('https://open.tiktokapis.com/v2/post/publish/status/fetch/', [
                'publish_id' => $publishId,
            ]);

            $state = $status->json('data.status');

            if ($state === 'PUBLISH_COMPLETE') {
                return ['success' => true, 'error' => null];
            }
            if ($state === 'FAILED') {
                return ['success' => false, 'error' => $status->json('data.fail_reason') ?? 'TikTok reported a failure.'];
            }

            sleep(3);
        }

        return ['success' => false, 'error' => 'TikTok did not finish processing in time.'];
    }
}
