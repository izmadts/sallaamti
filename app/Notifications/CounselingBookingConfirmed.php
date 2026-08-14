<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingConfirmed extends Notification implements ShouldQueue
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
            ->subject('Your Counseling Session is Confirmed')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your family counseling session has been confirmed.')
            ->line('Time: ' . $this->booking->scheduled_at->format('d M Y, h:i A'))
            ->line('Counselor: ' . $this->booking->counselor?->name)
            ->line('Contact method: ' . ucfirst(str_replace('_', ' ', $this->booking->contact_method)))
            ->action('View Booking', route('counseling.bookings.show', $this->booking))
            ->salutation('— The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '✅ Your counseling session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' has been confirmed.',
            'url' => route('counseling.bookings.show', $this->booking),
        ];
    }
}
