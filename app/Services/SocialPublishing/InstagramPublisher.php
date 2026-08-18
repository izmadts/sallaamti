<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class InstagramPublisher implements SocialPublisher
{
    use FormatsPostContent;

    private const GRAPH_VERSION = 'v19.0';

    // Instagram has no text-only post type and no separate login of its own —
    // the account here is a Business account whose posts are authenticated
    // with the linked Facebook Page's access token (see
    // SocialIntegrationController::callback(), which creates this row as a
    // side effect of connecting Facebook).
    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $igUserId = $account->external_account_id;
        $base = 'https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$igUserId}";

        $videoUrl = $this->videoUrl($post);
        $photoUrl = $this->photoUrl($post);

        if (!$videoUrl && !$photoUrl) {
            return ['success' => false, 'external_id' => null, 'error' => 'Instagram requires a photo or video — this post has neither.'];
        }

        $createParams = ['caption' => $this->caption($post, 2200), 'access_token' => $account->access_token];
        $createParams += $videoUrl ? ['media_type' => 'REELS', 'video_url' => $videoUrl] : ['image_url' => $photoUrl];

        $create = Http::asForm()->post("{$base}/media", $createParams);

        if ($create->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $this->extractError($create)];
        }

        $creationId = $create->json('id');

        if ($videoUrl && !$this->waitUntilProcessed($creationId, $account->access_token)) {
            return ['success' => false, 'external_id' => null, 'error' => 'Instagram video processing did not finish in time — it may still publish; check the account directly.'];
        }

        $publish = Http::asForm()->post('https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$igUserId}/media_publish", [
            'creation_id' => $creationId,
            'access_token' => $account->access_token,
        ]);

        if ($publish->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $this->extractError($publish)];
        }

        return ['success' => true, 'external_id' => $publish->json('id'), 'error' => null];
    }

    // Reels need the container to finish server-side processing before it
    // can be published — poll a bounded number of times rather than a fixed
    // sleep, since this runs inside a queued job and can afford to wait.
    private function waitUntilProcessed(string $creationId, string $accessToken, int $maxAttempts = 10): bool
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $status = Http::get('https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$creationId}", [
                'fields' => 'status_code',
                'access_token' => $accessToken,
            ])->json('status_code');

            if ($status === 'FINISHED') {
                return true;
            }
            if ($status === 'ERROR') {
                return false;
            }

            sleep(3);
        }

        return false;
    }

    private function extractError($response): string
    {
        return $response->json('error.message') ?? ('HTTP ' . $response->status());
    }
}
