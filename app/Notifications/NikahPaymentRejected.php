<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

// Mirrors NikahPaymentConfirmed's channel set (database/mail/FCM) — the
// gap this fills: NikahPaymentAdminController::reject() used to update the
// payment_status column and nothing else, leaving a member whose payment
// was rejected with zero signal that anything happened at all, let alone
// what to do about it. Nikah features stay gated behind a confirmed
// payment, so this is the one notification that actually tells them why.
class NikahPaymentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly NikahProfile $profile) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '⚠️ Action needed on your Nikah payment',
            'body' => 'Your payment proof was not accepted. Please verify your profile to unlock Nikah features.',
            'data' => ['type' => 'payment_rejected'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Action Needed — Your Nikah Payment Was Not Accepted')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('We were unable to confirm your Nikah verification fee payment.')
            ->line('Reason: ' . ($this->profile->payment_rejection_reason ?: 'Please check your payment details and try again.'))
            ->line('Please complete your registration by verifying your profile — your Nikah features (browsing matches, sending interests, and more) stay locked until payment is confirmed.')
            ->action('Resubmit Payment', route('nikah.payment'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '⚠️ Your Nikah payment was not accepted. Verify your profile to unlock Nikah features.',
            'url' => route('nikah.payment'),
        ];
    }
}
