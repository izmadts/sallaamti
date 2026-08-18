<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class FacebookPublisher implements SocialPublisher
{
    use FormatsPostContent;

    private const GRAPH_VERSION = 'v19.0';

    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $pageId = $account->external_account_id;
        $base = 'https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$pageId}";

        if ($videoUrl = $this->videoUrl($post)) {
            $response = Http::asForm()->post("{$base}/videos", [
                'file_url' => $videoUrl,
                'description' => $this->caption($post),
                'access_token' => $account->access_token,
            ]);
        } elseif ($photoUrl = $this->photoUrl($post)) {
            $response = Http::asForm()->post("{$base}/photos", [
                'url' => $photoUrl,
                'caption' => $this->caption($post),
                'access_token' => $account->access_token,
            ]);
        } else {
            $response = Http::asForm()->post("{$base}/feed", [
                'message' => $this->caption($post),
                'access_token' => $account->access_token,
            ]);
        }

        if ($response->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $this->extractError($response)];
        }

        $data = $response->json();

        return ['success' => true, 'external_id' => $data['post_id'] ?? $data['id'] ?? null, 'error' => null];
    }

    private function extractError($response): string
    {
        return $response->json('error.message') ?? ('HTTP ' . $response->status());
    }
}
