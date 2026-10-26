<?php

namespace App\Notifications;

use App\Models\CommunityPost;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommunityAnnouncementPosted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CommunityPost $post) {}

    public function via($notifiable): array
    {
        // 'webpush' reaches a browser with a registered Web Push
        // subscription; FcmChannel is the one that actually reaches the
        // member's phone — this is a mobile-first feature, so both matter,
        // but FcmChannel is the one that makes "every member gets notified"
        // true in practice.
        return ['database', 'webpush', FcmChannel::class];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('📢 New Announcement')
            ->body($this->post->title)
            ->url(route('wall.index'));
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '📢 New Announcement',
            'body' => Str::limit($this->post->title, 120),
            'data' => ['type' => 'community_announcement', 'community_post_id' => (string) $this->post->id],
            'app' => 'sallaamti_app',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '📢 ' . Str::limit($this->post->title, 100),
            'community_post_id' => $this->post->id,
            'url' => route('wall.index'),
        ];
    }
}
