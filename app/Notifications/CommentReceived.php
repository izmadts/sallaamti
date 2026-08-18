<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CommentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment, public User $commenter) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush'];
    }

    protected function subject(): string
    {
        return $this->comment->commentable instanceof \App\Models\DuaRequest ? 'dua' : 'post';
    }

    protected function verb(): string
    {
        return $this->comment->parent_id ? 'replied to your comment on' : 'commented on your';
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->commenter->name . " {$this->verb()} {$this->subject()}")
            ->body(\Illuminate\Support\Str::limit($this->comment->body, 100))
            ->url(route('wall.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->commenter->name . " {$this->verb()} {$this->subject()}: " . \Illuminate\Support\Str::limit($this->comment->body, 80),
            'comment_id' => $this->comment->id,
            'url' => route('wall.index'),
        ];
    }
}
