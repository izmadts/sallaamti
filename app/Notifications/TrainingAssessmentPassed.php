<?php

namespace App\Notifications;

use App\Models\MatchmakerApplication;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Trainee-access addendum (15/16 Nov 2026) — sent when a Training-stage
// counselor passes the in-app quiz and is auto-promoted to Assessed (see
// TrainingQuizController::submit()). Mirrors NikahCounselorCertified's
// channel/shape.
class TrainingAssessmentPassed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakerApplication $application, public int $score, public int $total) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '🎉 Assessment passed!',
            'body' => "You scored {$this->score}/{$this->total} and are now at the Assessed stage — more features just unlocked.",
            'data' => ['type' => 'training_assessment_passed'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Assessment Passed — You\'re Now Assessed!')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("You scored {$this->score}/{$this->total} on the Training Guide assessment.")
            ->line('You\'ve moved from Training to Assessed — Requirements, Consent, and Browse are now unlocked in your app.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'training_assessment_passed',
            'message' => "🎉 You scored {$this->score}/{$this->total} and are now Assessed — more features unlocked.",
        ];
    }
}
