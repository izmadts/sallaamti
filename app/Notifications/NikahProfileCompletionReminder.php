<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahProfileCompletionReminder extends Notification
{
    use Queueable;

    public function __construct(public NikahProfile $profile) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('You\'re almost there — finish your Sallaamti Nikah profile')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('You started creating your Nikah profile on Sallaamti, but there\'s one step left before our team can review and verify it.');

        if ($this->profile->payment_status === 'rejected') {
            $mail->line('Your last payment proof was rejected: ' . $this->profile->payment_rejection_reason)
                ->line('Please resubmit your verification fee payment so we can proceed.')
                ->action('Resubmit Payment', route('nikah.payment'));
        } else {
            $mail->line('Please complete your verification fee payment so our team can verify your CNIC and activate your profile in search.')
                ->action('Complete Payment', route('nikah.payment'));
        }

        return $mail->line('Once verified, your profile becomes visible to serious, verified matches — we don\'t want you to miss out. May Allah make this journey easy for you, and best of luck finding your match, InshaAllah!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💍 Complete your Nikah profile\'s remaining step to get verified and start finding your match!',
            'url' => route('nikah.payment'),
        ];
    }
}
