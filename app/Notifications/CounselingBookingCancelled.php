<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CounselingBooking $booking) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '❌ Counseling session cancelled',
            'body' => 'Your session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' was cancelled.',
            'data' => ['type' => 'counseling_booking_cancelled', 'booking_id' => (string) $this->booking->id],
        ];
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
