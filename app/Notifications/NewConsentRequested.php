<?php

namespace App\Notifications;

use App\Models\MatchmakingConsent;
use App\Models\MatchmakingConsentRequest;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fired from Matchmaker\ClientController::requestConsent() — previously a
// client was never notified a consent request existed, only finding out
// via the matchmaker manually re-sharing the /p/{lead} link. Mirrors
// LeadClientMessageReceived's exact channel/pattern.
class NewConsentRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakingConsentRequest $consentRequest) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    private function label(): string
    {
        return explode(' — ', MatchmakingConsent::TYPES[$this->consentRequest->consent_type] ?? $this->consentRequest->consent_type)[0];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '✅ Consent requested',
            'body' => 'Your Nikah Counselor is asking you to confirm: ' . $this->label(),
            'data' => ['type' => 'consent_requested', 'lead_id' => (string) $this->consentRequest->lead_id, 'consent_request_id' => (string) $this->consentRequest->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Consent Requested — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah Counselor is asking you to confirm: ' . $this->label())
            ->action('Open Sallaamti App', url('/'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '✅ Your Nikah Counselor requested your consent: ' . $this->label(),
            'lead_id' => $this->consentRequest->lead_id,
            'consent_request_id' => $this->consentRequest->id,
        ];
    }
}
