<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Mirrors VolunteerApplicationDecided's shape — one class, a boolean flag,
// both decisions. Unlike that one, nothing else already emails the student
// about this decision, so mail stays in via() here.
class CourseCertificateDecided extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Certificate $certificate, public bool $approved) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $title = $this->certificate->title ?? $this->certificate->course?->title ?? 'your course';

        $message = (new MailMessage)->greeting('Assalamu Alaikum ' . $notifiable->name . '!');

        return $this->approved
            ? $message->subject('Certificate Approved — ' . $title)
                ->line("Congratulations! Your certificate for {$title} has been approved and is ready to download.")
                ->action('View My Certificates', route('certificate.index'))
            : $message->subject('Certificate Request Update — ' . $title)
                ->line("Your certificate request for {$title} wasn't approved this time.")
                ->when($this->certificate->rejection_reason, fn ($m) => $m->line($this->certificate->rejection_reason))
                ->action('View My Certificates', route('certificate.index'));
    }

    public function toArray($notifiable): array
    {
        $title = $this->certificate->title ?? $this->certificate->course?->title ?? 'your course';

        return [
            'type' => $this->approved ? 'certificate_approved' : 'certificate_rejected',
            'message' => $this->approved
                ? "🎓 Your certificate for {$title} was approved — download it now."
                : "Your certificate request for {$title} wasn't approved this time.",
            'url' => route('certificate.index'),
        ];
    }

    public function toFcm($notifiable): array
    {
        $title = $this->certificate->title ?? $this->certificate->course?->title ?? 'your course';

        return [
            'title' => $this->approved ? '🎓 Certificate approved!' : 'Certificate request update',
            'body' => $this->approved
                ? "Your certificate for {$title} is ready to download."
                : "Your certificate request for {$title} wasn't approved this time.",
            'data' => ['type' => $this->approved ? 'certificate_approved' : 'certificate_rejected', 'certificate_id' => (string) $this->certificate->id],
            'app' => 'sallaamti_app',
        ];
    }
}
