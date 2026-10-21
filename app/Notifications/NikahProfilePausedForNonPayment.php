<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

// Sent once by SendNikahPaymentDeadlineReminders the moment a profile's
// 7-day payment window actually runs out — a one-click, no-approval-needed
// "Extend 7 Days" reverses this (see NikahProfile::extendPaymentDeadline()),
// same as NikahProfileCompletionReminder's own payment link.
class NikahProfilePausedForNonPayment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahProfile $profile) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '⏸️ Your Nikah profile was paused',
            'body' => $this->profile->canExtendPaymentDeadline()
                ? 'The 7-day payment window ran out, so your profile is hidden from search. Tap to extend 7 more days.'
                : 'The 7-day payment window ran out and you\'re out of self-service extensions. Contact us on WhatsApp to reactivate.',
            'data' => ['type' => 'nikah_payment_reminder'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your Nikah profile has been paused — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah profile\'s 7-day window to complete the verification fee payment has run out, so it\'s now paused and hidden from search.')
            ->line('Nothing is lost — your profile and all its details are still saved exactly as you left them.');

        if ($this->profile->canExtendPaymentDeadline()) {
            $mail->line('If you\'d like more time, you can extend the deadline by another 7 days yourself, right away, no need to contact us.')
                ->action('Extend 7 Days', route('nikah.show'));
        } else {
            $mail->line('You\'ve used all ' . \App\Models\NikahProfile::MAX_PAYMENT_DEADLINE_EXTENSIONS . ' self-service extensions available for this profile — please reach out to us on WhatsApp and we\'ll help reactivate it.')
                ->action('Contact Support on WhatsApp', whatsapp_link('Hi, my Nikah profile was paused and I\'ve used all my self-service extensions — can you help reactivate it?'));
        }

        return $mail->line('Or complete the payment now to get verified and skip the wait entirely.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->profile->canExtendPaymentDeadline()
                ? '⏸️ Your Nikah profile was paused — the 7-day payment window ran out. Extend it or pay now to reactivate.'
                : '⏸️ Your Nikah profile was paused and you\'re out of self-service extensions. Contact us on WhatsApp or pay now to reactivate.',
            'url' => route('nikah.show'),
        ];
    }
}
