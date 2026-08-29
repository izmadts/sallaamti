<?php

namespace App\Notifications;

use App\Models\MatchmakerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// To admin only — a counselor tapping "Request Card Dispatch" in the app
// asks admin to print and mail their physical ID card. No FCM leg: admin
// doesn't have the Nikah Counselor app, this is mail/database only, same
// as NewPackagePaymentSubmitted and NewNikahVerificationDocumentsSubmitted.
class NikahCounselorCardRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakerApplication $application) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Counselor ID Card Requested — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line($this->application->full_name . ' (' . $this->application->counselor_code . ') requested their physical ID card be printed and mailed.')
            ->line('Mailing address: ' . $this->application->address . ', ' . $this->application->area . ', ' . $this->application->country)
            ->action('View Counselor', route('admin.matchmaker-applications.show', $this->application));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '📮 ' . $this->application->full_name . ' requested their ID card be mailed.',
            'url' => route('admin.matchmaker-applications.show', $this->application),
        ];
    }
}
