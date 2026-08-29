<?php

namespace App\Notifications;

use App\Models\MatchmakerApplication;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

// Sent to both the promoted counselor (celebratory) and admins (this
// changes their commission tier — see MatchmakerApplication::
// PROMOTION_THRESHOLDS's own comment — so it's never a silent change).
// $forAdmin swaps the tone/detail rather than being two near-identical
// classes.
class CounselorLevelPromoted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakerApplication $application, public bool $forAdmin = false)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    // Only the counselor's own leg gets a push — admin doesn't have the
    // counselor app, and would get pushed to nothing anyway (no device
    // tokens registered for that role), but returning null here is clearer
    // than relying on that incidentally.
    public function toFcm($notifiable): ?array
    {
        if ($this->forAdmin) {
            return null;
        }

        $label = MatchmakerApplication::LEVELS[$this->application->level];

        return [
            'title' => '🎉 You\'ve been promoted!',
            'body' => "Your consistent, verified work earned you a new level: {$label}. Higher commission rates now apply.",
            'data' => ['type' => 'level_promoted', 'level' => $this->application->level],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $label = MatchmakerApplication::LEVELS[$this->application->level];

        if ($this->forAdmin) {
            return (new MailMessage)
                ->subject('Counselor Auto-Promoted — Sallaamti')
                ->greeting('Assalamu Alaikum!')
                ->line($this->application->full_name . ' was automatically promoted to ' . $label . ' — commission tier has changed accordingly.')
                ->action('View Counselor', route('admin.matchmaker-applications.show', $this->application));
        }

        return (new MailMessage)
            ->subject('Congratulations — You\'ve Been Promoted! — Sallaamti')
            ->greeting('Assalamu Alaikum, ' . $this->application->full_name . '!')
            ->line('Your consistent, verified work has earned you a new level: **' . $label . '**.')
            ->line('This reflects both your volume and quality of work — keep it up!')
            ->action('View Your Performance', route('matchmaker.performance.index'));
    }

    public function toArray($notifiable): array
    {
        $label = MatchmakerApplication::LEVELS[$this->application->level];

        return [
            'message' => $this->forAdmin
                ? '⬆️ ' . $this->application->full_name . ' was auto-promoted to ' . $label . '.'
                : '🎉 Congratulations — you\'ve been promoted to ' . $label . '!',
            'url' => $this->forAdmin ? route('admin.matchmaker-applications.show', $this->application) : route('matchmaker.performance.index'),
        ];
    }
}
