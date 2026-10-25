<?php

namespace App\Notifications;

use App\Models\CommunityPost;
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
        return ['database', 'webpush'];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('📢 New Announcement')
            ->body($this->post->title)
            ->url(route('wall.index'));
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
