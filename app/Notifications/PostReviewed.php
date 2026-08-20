<?php

namespace App\Notifications;

use App\Models\Post;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PostReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public bool $approved) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->approved ? 'Your post is live!' : 'Your post needs changes')
            ->body($this->approved
                ? "\"{$this->post->title}\" was approved and is now public."
                : "\"{$this->post->title}\" wasn't approved: {$this->post->rejection_reason}")
            ->url($this->approved ? route('posts.show', $this->post) : route('posts.mine'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->approved
                ? "Your post \"{$this->post->title}\" was approved and is now public."
                : "Your post \"{$this->post->title}\" wasn't approved: {$this->post->rejection_reason}",
            'post_id' => $this->post->id,
            'url' => $this->approved ? route('posts.show', $this->post) : route('posts.mine'),
        ];
    }
}
