<?php

namespace App\Services\SocialPublishing;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Models\Setting;
use App\Services\SocialPublishing\Concerns\FormatsPostContent;
use App\Services\SocialPublishing\Contracts\SocialPublisher;
use Google\Client as GoogleClient;
use Google\Http\MediaFileUpload;
use Google\Service\YouTube as YouTubeService;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;

class YoutubePublisher implements SocialPublisher
{
    use FormatsPostContent;

    // YouTube has no photo/text post type — a Reel/Short needs an actual
    // video file, unlike every other platform here which can fall back to
    // a text or image post.
    public function publish(CommunityPost $post, SocialAccount $account): array
    {
        $path = $this->videoPath($post);

        if (!$path) {
            return ['success' => false, 'external_id' => null, 'error' => 'YouTube requires a video — this post has none.'];
        }

        $client = new GoogleClient();
        $client->setClientId(Setting::get('youtube_client_id') ?: config('services.youtube.client_id'));
        $client->setClientSecret(Setting::get('youtube_client_secret') ?: config('services.youtube.client_secret'));
        $client->refreshToken($account->refresh_token);
        $client->setDefer(true);

        $youtube = new YouTubeService($client);

        $snippet = new VideoSnippet();
        $snippet->setTitle(\Illuminate\Support\Str::limit($post->title, 100, ''));
        $snippet->setDescription(strip_tags($post->body));
        if (!empty($post->tags)) {
            $snippet->setTags($post->tags);
        }

        $status = new VideoStatus();
        $status->setPrivacyStatus('public');

        $video = new Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $insertRequest = $youtube->videos->insert('status,snippet', $video);

        $chunkSize = 5 * 1024 * 1024;
        $media = new MediaFileUpload($client, $insertRequest, 'video/*', null, true, $chunkSize);
        $media->setFileSize(filesize($path));

        $handle = fopen($path, 'rb');
        $uploadStatus = false;

        try {
            while (!$uploadStatus && !feof($handle)) {
                $chunk = fread($handle, $chunkSize);
                $uploadStatus = $media->nextChunk($chunk);
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'external_id' => null, 'error' => $e->getMessage()];
        } finally {
            fclose($handle);
            $client->setDefer(false);
        }

        return ['success' => true, 'external_id' => $uploadStatus['id'] ?? null, 'error' => null];
    }
}
