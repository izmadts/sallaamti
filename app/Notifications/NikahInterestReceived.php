<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NikahInterestReceived extends Notification
{
    use Queueable;

    public function __construct(public NikahInterest $interest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'You received a new interest request.',
            'interest_id' => $this->interest->id,
            'url' => route('nikah.interests'),
        ];
    }
}
