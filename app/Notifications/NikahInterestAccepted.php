<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NikahInterestAccepted extends Notification
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
            'message' => 'Your interest request was accepted! Contact details are now visible.',
            'interest_id' => $this->interest->id,
            'url' => route('nikah.interests'),
        ];
    }
}
