<?php

namespace App\Notifications;

use App\Models\AdminMessage;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AdminMessageReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AdminMessage $message) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush', FcmChannel::class];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('💬 Sallaamti Support replied')
            ->body(Str::limit($this->message->body, 100));
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💬 Sallaamti Support replied',
            'body' => Str::limit($this->message->body, 100),
            'data' => ['type' => 'admin_message_reply'],
            'app' => 'sallaamti_app',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💬 Sallaamti Support: ' . Str::limit($this->message->body, 80),
        ];
    }
}
