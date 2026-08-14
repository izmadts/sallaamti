<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingCancelled extends Notification
{
    use Queueable;

    public function __construct(public CounselingBooking $booking) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Counseling Session Cancelled')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your counseling session scheduled for ' . $this->booking->scheduled_at->format('d M Y, h:i A') . ' has been cancelled.')
            ->when($this->booking->cancellation_reason, fn ($mail) => $mail->line('Reason: ' . $this->booking->cancellation_reason))
            ->salutation('— The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '❌ Your counseling session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' was cancelled.',
            'url' => route('counseling.bookings.index'),
        ];
    }
}
