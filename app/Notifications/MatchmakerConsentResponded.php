<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\MatchmakingConsent;
use App\Models\MatchmakingConsentRequest;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchmakerConsentResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakingConsentRequest $consentRequest, public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        $granted = $this->consentRequest->status === 'granted';

        return [
            'title' => $granted ? '✅ Consent confirmed' : '❌ Consent declined',
            'body' => "{$this->lead->name} " . ($granted ? 'confirmed' : 'declined') . " their \"{$this->label()}\" consent request.",
            'data' => ['type' => 'consent_responded', 'lead_id' => (string) $this->lead->id],
        ];
    }

    private function label(): string
    {
        return explode(' — ', MatchmakingConsent::TYPES[$this->consentRequest->consent_type])[0];
    }

    public function toMail($notifiable): MailMessage
    {
        $granted = $this->consentRequest->status === 'granted';

        return (new MailMessage)
            ->subject(($granted ? 'Consent Confirmed' : 'Consent Declined') . ' — Sallaamti Matchmaker')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->lead->name} " . ($granted ? 'confirmed' : 'declined') . " their \"{$this->label()}\" consent request.")
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        $granted = $this->consentRequest->status === 'granted';

        return [
            'message' => ($granted ? '✅ ' : '❌ ') . "{$this->lead->name} " . ($granted ? 'confirmed' : 'declined') . " their \"{$this->label()}\" consent request.",
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }
}
