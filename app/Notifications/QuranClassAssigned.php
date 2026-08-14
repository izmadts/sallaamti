<?php

namespace App\Notifications;

use App\Models\QuranClassGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QuranClassAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranClassGroup $group) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "You have been assigned to {$this->group->group_name} — {$this->group->class_time}. Please complete your monthly payment to join.",
            'url' => route('quran-live.my-class'),
        ];
    }
}
