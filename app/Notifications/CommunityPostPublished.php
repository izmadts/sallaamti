<?php

namespace App\Notifications;

use App\Models\CommunityPost;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

// To every member, for a regular (non-announcement) Community Post —
// CommunityAnnouncementPosted stays the distinct, more urgent-sounding
// notification for a post admin has actually marked an announcement; this
// is the quieter everyday version, same delivery channels, different
// wording and `type` (so the app's notification_route_resolver can still
// tell them apart even though both just open the Wall).
class CommunityPostPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CommunityPost $post) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush', FcmChannel::class];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('📰 New Post on Sallaamti Wall')
            ->body(Str::limit($this->post->title, 120))
            ->url(route('wall.index'));
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '📰 New Post on Sallaamti Wall',
            'body' => Str::limit($this->post->title, 120),
            'data' => ['type' => 'community_post', 'community_post_id' => (string) $this->post->id],
            'app' => 'sallaamti_app',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            // See CommentReceived::toArray()'s own comment — same gap/fix.
            'type' => 'community_post',
            'message' => '📰 ' . Str::limit($this->post->title, 100),
            'community_post_id' => $this->post->id,
            'url' => route('wall.index'),
        ];
    }
}
