<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingRequested extends Notification implements ShouldQueue
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
            'title' => '🤝 New counseling session request',
            'body' => 'Requested for ' . $this->booking->scheduled_at->format('d M, h:i A'),
            'data' => ['type' => 'counseling_booking_requested', 'booking_id' => (string) $this->booking->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Family Counseling Session Request')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('A member has requested a counseling session with you.')
            ->line('Requested time: ' . $this->booking->scheduled_at->format('d M Y, h:i A'))
            ->line('Contact method: ' . ucfirst(str_replace('_', ' ', $this->booking->contact_method)))
            ->action('View Booking', route('counselor.bookings.index'))
            ->salutation('— The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🤝 New counseling session request for ' . $this->booking->scheduled_at->format('d M, h:i A'),
            'url' => route('counselor.bookings.index'),
        ];
    }
}
