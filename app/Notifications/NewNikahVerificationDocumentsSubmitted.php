<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewNikahVerificationDocumentsSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahProfile $profile) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification Documents Submitted — Sallaamti Nikah')
            ->greeting('Assalamu Alaikum!')
            ->line('A remotely-registered profile just self-uploaded their CNIC/photo and is ready for verification review.')
            ->line('**Member:** ' . $this->profile->user->name)
            ->action('Review Verification', route('admin.nikah.verifications'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🪪 ' . $this->profile->user->name . ' uploaded their verification documents — ready for review.',
            'url' => route('admin.nikah.verifications'),
        ];
    }
}
