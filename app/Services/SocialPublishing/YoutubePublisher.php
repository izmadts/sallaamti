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

        $description = strip_tags($post->body);
        if ($hashtagsLine = $this->hashtagsLine($post)) {
            // YouTube also recognizes '#' hashtags placed in the description
            // itself (shown above the title on the watch page), separate
            // from the structured keyword tags field below — worth having
            // both since they serve different discovery surfaces.
            $description .= "\n\n" . $hashtagsLine;
        }
        $snippet->setDescription(\Illuminate\Support\Str::limit($description, 5000, ''));

        // Merge the admin's hashtag/keyword field with the Wall's own
        // category tags (Activity/Event/Sermon etc., already on $post->tags)
        // — both are genuinely useful keywords for YouTube's search/
        // discovery. keywordTags() caps the hashtag-derived portion, but
        // the wall tags get merged back in afterward, so re-cap the final
        // combined list too — YouTube hard-rejects the whole upload if the
        // combined tags exceed 500 characters.
        $keywordTags = $this->capKeywordLength(
            array_values(array_unique(array_merge($this->keywordTags($post), $post->tags ?? [])))
        );
        if (!empty($keywordTags)) {
            $snippet->setTags($keywordTags);
        }

        $snippet->setTitle(\Illuminate\Support\Str::limit($post->title, 100, ''));

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

    // YouTube hard-rejects a video upload if the tags array's combined
    // length exceeds 500 characters — drop trailing tags once adding
    // another would cross that, rather than let the whole upload fail.
    private function capKeywordLength(array $tags, int $maxCombinedLength = 500): array
    {
        $kept = [];
        $length = 0;

        foreach ($tags as $tag) {
            $length += strlen($tag) + 1;
            if ($length > $maxCombinedLength) {
                break;
            }
            $kept[] = $tag;
        }

        return $kept;
    }
}
