<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
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
        return ['mail', 'database'];
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
