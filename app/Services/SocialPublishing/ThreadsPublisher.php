<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

// Threads Graph API — a two-step create-then-publish flow like Instagram's
// (see InstagramPublisher), but on graph.threads.net rather than
// graph.facebook.com, and with its own account id (the Threads user id
// returned at connect time, stored as external_account_id).
class ThreadsPublisher implements SocialPublisher
{
    use FormatsPostContent;

    private const API_VERSION = 'v1.0';

    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $userId = $account->external_account_id;
        $base = 'https://graph.threads.net/' . self::API_VERSION . "/{$userId}/threads";

        $videoUrl = $this->videoUrl($post);
        $photoUrl = $this->photoUrl($post);

        $createParams = ['text' => $this->caption($post, 500), 'access_token' => $account->access_token];
        $createParams += match (true) {
            (bool) $videoUrl => ['media_type' => 'VIDEO', 'video_url' => $videoUrl],
            (bool) $photoUrl => ['media_type' => 'IMAGE', 'image_url' => $photoUrl],
            default => ['media_type' => 'TEXT'],
        };

        $create = Http::asForm()->post($base, $createParams);

        if ($create->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $this->extractError($create)];
        }

        $creationId = $create->json('id');

        if (($videoUrl || $photoUrl) && !$this->waitUntilProcessed($creationId, $account->access_token)) {
            return ['success' => false, 'external_id' => null, 'error' => 'Threads media processing did not finish in time — it may still publish; check the account directly.'];
        }

        $publish = Http::asForm()->post('https://graph.threads.net/' . self::API_VERSION . "/{$userId}/threads_publish", [
            'creation_id' => $creationId,
            'access_token' => $account->access_token,
        ]);

        if ($publish->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $this->extractError($publish)];
        }

        return ['success' => true, 'external_id' => $publish->json('id'), 'error' => null];
    }

    // Same bounded-poll pattern as InstagramPublisher::waitUntilProcessed —
    // Threads' container needs to finish server-side processing before it
    // can be published, and this runs inside a queued job so waiting is fine.
    private function waitUntilProcessed(string $creationId, string $accessToken, int $maxAttempts = 10): bool
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $status = Http::withToken($accessToken)->get('https://graph.threads.net/' . self::API_VERSION . "/{$creationId}", [
                'fields' => 'status',
            ])->json('status');

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
