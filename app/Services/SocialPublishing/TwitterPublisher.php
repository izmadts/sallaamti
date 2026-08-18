<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class TwitterPublisher implements SocialPublisher
{
    use FormatsPostContent;

    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $token = $account->access_token;

        try {
            $mediaId = $this->videoPath($post)
                ? $this->uploadVideo($this->videoPath($post), $token)
                : ($this->photoUrl($post) ? $this->uploadPhoto($post, $token) : null);
        } catch (\Throwable $e) {
            return ['success' => false, 'external_id' => null, 'error' => 'Media upload failed: ' . $e->getMessage()];
        }

        $body = ['text' => $this->caption($post, 280)];
        if ($mediaId) {
            $body['media'] = ['media_ids' => [$mediaId]];
        }

        $response = Http::withToken($token)->post('https://api.twitter.com/2/tweets', $body);

        if ($response->failed()) {
            return ['success' => false, 'external_id' => null, 'error' => $response->json('detail') ?? ('HTTP ' . $response->status())];
        }

        return ['success' => true, 'external_id' => $response->json('data.id'), 'error' => null];
    }

    // Images fit X's "simple" media upload (single request, base64 body).
    private function uploadPhoto(CommunityPost $post, string $token): ?string
    {
        $path = \Illuminate\Support\Facades\Storage::disk('public')->path($post->photo);
        $response = Http::withToken($token)->asForm()->post('https://upload.twitter.com/1.1/media/upload.json', [
            'media_data' => base64_encode(file_get_contents($path)),
        ]);

        return $response->successful() ? (string) $response->json('media_id_string') : null;
    }

    // Video needs X's chunked INIT/APPEND/FINALIZE upload flow, then a
    // bounded poll for FINALIZE's async processing before it can be
    // attached to a tweet.
    private function uploadVideo(string $path, string $token): ?string
    {
        $bytes = file_get_contents($path);
        $totalBytes = strlen($bytes);

        $init = Http::withToken($token)->asForm()->post('https://upload.twitter.com/1.1/media/upload.json', [
            'command' => 'INIT',
            'total_bytes' => $totalBytes,
            'media_type' => 'video/mp4',
            'media_category' => 'tweet_video',
        ]);

        if ($init->failed()) {
            return null;
        }

        $mediaId = $init->json('media_id_string');
        $chunkSize = 4 * 1024 * 1024;

        foreach (array_chunk(str_split($bytes, $chunkSize), 1) as $index => $chunk) {
            Http::withToken($token)->asMultipart()->post('https://upload.twitter.com/1.1/media/upload.json?' . http_build_query([
                'command' => 'APPEND',
                'media_id' => $mediaId,
                'segment_index' => $index,
            ]), [
                ['name' => 'media', 'contents' => $chunk[0]],
            ]);
        }

        $finalize = Http::withToken($token)->asForm()->post('https://upload.twitter.com/1.1/media/upload.json', [
            'command' => 'FINALIZE',
            'media_id' => $mediaId,
        ]);

        if (!$this->waitUntilProcessed($mediaId, $token, $finalize->json('processing_info'))) {
            return null;
        }

        return $mediaId;
    }

    private function waitUntilProcessed(string $mediaId, string $token, ?array $processingInfo, int $maxAttempts = 15): bool
    {
        if (!$processingInfo) {
            return true; // FINALIZE returned no processing_info — it's already ready.
        }

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            if (($processingInfo['state'] ?? null) === 'succeeded') {
                return true;
            }
            if (($processingInfo['state'] ?? null) === 'failed') {
                return false;
            }

            sleep(min((int) ($processingInfo['check_after_secs'] ?? 3), 10));

            $status = Http::withToken($token)->get('https://upload.twitter.com/1.1/media/upload.json', [
                'command' => 'STATUS',
                'media_id' => $mediaId,
            ]);
            $processingInfo = $status->json('processing_info');
        }

        return false;
    }
}
