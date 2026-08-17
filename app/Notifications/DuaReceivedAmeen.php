<?php

namespace App\Notifications;

use App\Models\DuaRequest;
use App\Models\User;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DuaReceivedAmeen extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DuaRequest $duaRequest, public User $reactor) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Someone said Ameen to your dua 🤲')
            ->body($this->reactor->name . ' reacted to your dua on Sallaamti.')
            ->url(route('wall.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->reactor->name . ' said Ameen to your dua.',
            'dua_request_id' => $this->duaRequest->id,
            'url' => route('wall.index'),
        ];
    }
}
