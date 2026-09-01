<?php

namespace App\Notifications;

use App\Models\QuranAdmission;
use App\Models\QuranClassGroup;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuranClassReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranClassGroup $group, public QuranAdmission $admission) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Today's Quran Class — {$this->admission->student_name}")
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->admission->student_name} has a Quran class today with {$this->group->group_name}.")
            ->line("Time: {$this->group->class_time}")
            ->action('View My Class', route('quran-live.my-class'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "📅 {$this->admission->student_name} has class today at {$this->group->class_time} — {$this->group->group_name}.",
            'url' => route('quran-live.my-class'),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Quran class today',
            'body' => "{$this->admission->student_name} has class today at {$this->group->class_time} — {$this->group->group_name}.",
            'data' => ['type' => 'quran_class_today', 'group_id' => (string) $this->group->id],
            'app' => 'sallaamti_app',
        ];
    }
}
