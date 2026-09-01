<?php

namespace App\Notifications;

use App\Models\QuranClassGroup;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

// Fires the moment a teacher posts today's join link (Teacher\
// QuranTeacherController::postDailyLink()) — closes the gap where a student
// had no way to know the link was actually up beyond refreshing "My Class"
// and hoping. Database + FCM only, deliberately no mail: by the time an
// email would land, the class may already be starting — this is meant to be
// seen right away or not at all, not read later.
class QuranClassLinkPosted extends Notification implements ShouldQueue
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
            'type' => 'quran_class_link_posted',
            'message' => "🎥 Today's class link is up for {$this->group->group_name}.",
            'url' => route('quran-live.my-class'),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => "Today's class link is up",
            'body' => "{$this->group->group_name} — tap to join.",
            'data' => ['type' => 'quran_class_link_posted', 'group_id' => (string) $this->group->id],
            'app' => 'sallaamti_app',
        ];
    }
}
