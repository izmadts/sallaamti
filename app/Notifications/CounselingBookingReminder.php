<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingReminder extends Notification implements ShouldQueue
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
            'title' => '⏰ Upcoming counseling session',
            'body' => 'Your session is on ' . $this->booking->scheduled_at->format('d M, h:i A'),
            'data' => ['type' => 'counseling_booking_reminder', 'booking_id' => (string) $this->booking->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: Upcoming Counseling Session')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('This is a reminder about your upcoming family counseling session.')
            ->line('Time: ' . $this->booking->scheduled_at->format('d M Y, h:i A'))
            ->line('Contact method: ' . ucfirst(str_replace('_', ' ', $this->booking->contact_method)))
            ->salutation('— The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '⏰ Reminder: your counseling session is on ' . $this->booking->scheduled_at->format('d M, h:i A'),
            'url' => route('counseling.bookings.show', $this->booking),
        ];
    }
}
