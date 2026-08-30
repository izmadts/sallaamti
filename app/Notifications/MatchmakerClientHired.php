<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fired when a self-service member (already has their own NikahProfile)
// chooses to bring in a counselor from within the app — see
// Api\V1\NikahHireCounselorController::hire(). Modeled directly on
// MatchmakerClientRegistered, the closest existing equivalent (a client
// reaching a milestone on their own, with nothing for the counselor to
// approve first).
class MatchmakerClientHired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '🤝 A member chose you as their counselor',
            'body' => "{$this->lead->name} selected you to help with their Nikah search — they already have a verified profile.",
            'data' => ['type' => 'client_hired', 'lead_id' => (string) $this->lead->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A Member Chose You as Their Counselor — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->lead->name} has a verified Nikah profile already and just chose you to help with their search.")
            ->line('They can message you directly from the client conversation, and their next step is choosing a package.')
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "🤝 {$this->lead->name} chose you as their Nikah Counselor.",
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }
}
