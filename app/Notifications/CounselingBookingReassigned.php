<?php

namespace App\Notifications;

use App\Models\CounselingBooking;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingBookingReassigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CounselingBooking $booking,
        public bool $forCounselor,
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => $this->forCounselor ? '📋 Session assigned to you' : '🔄 Counselor updated',
            'body' => $this->forCounselor
                ? 'A session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' needs your confirmation.'
                : 'Your session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' now has a new counselor.',
            'data' => ['type' => 'counseling_booking_reassigned', 'booking_id' => (string) $this->booking->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting('Assalamu Alaikum ' . $notifiable->name . '!');

        if ($this->forCounselor) {
            $mail->subject('A Counseling Session Has Been Assigned to You')
                ->line('A family counseling session has been reassigned to you.')
                ->line('Time: ' . $this->booking->scheduled_at->format('d M Y, h:i A'))
                ->line('Please confirm it at your earliest convenience.')
                ->action('View Booking', route('counselor.bookings.show', $this->booking));
        } else {
            $mail->subject('Your Counseling Session — Counselor Updated')
                ->line('Your family counseling session has been reassigned to a different counselor.')
                ->line('Time: ' . $this->booking->scheduled_at->format('d M Y, h:i A'))
                ->line('New counselor: ' . $this->booking->counselor?->name)
                ->line('The new counselor will need to confirm the session — we\'ll let you know once they do.')
                ->action('View Booking', route('counseling.bookings.show', $this->booking));
        }

        return $mail->salutation('— The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->forCounselor
                ? '📋 A counseling session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' has been assigned to you.'
                : '🔄 Your counseling session on ' . $this->booking->scheduled_at->format('d M, h:i A') . ' has a new counselor.',
            'url' => $this->forCounselor
                ? route('counselor.bookings.show', $this->booking)
                : route('counseling.bookings.show', $this->booking),
        ];
    }
}
