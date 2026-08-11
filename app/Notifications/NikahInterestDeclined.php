<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NikahInterestDeclined extends Notification
{
    use Queueable;

    public function __construct(public NikahInterest $interest) {}

    // In-app only — deliberately no email for a decline, so it's visible if the
    // sender checks the site but doesn't land as an unsolicited rejection email.
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Your interest request was declined.',
            'interest_id' => $this->interest->id,
            'url' => route('nikah.interests'),
        ];
    }
}
