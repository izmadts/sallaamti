<?php

namespace App\Notifications;

use App\Models\QuranClassGroup;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QuranClassAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranClassGroup $group) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "You have been assigned to {$this->group->group_name} — {$this->group->class_time}. Please complete your monthly payment to join.",
            'url' => route('quran-live.my-class'),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Assigned to a Quran class',
            'body' => "You've been assigned to {$this->group->group_name} — complete your monthly payment to join.",
            'data' => ['type' => 'quran_class_assigned', 'group_id' => (string) $this->group->id],
            'app' => 'sallaamti_app',
        ];
    }
}
