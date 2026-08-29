<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fires when a lead is created with, or reassigned to, a counselor —
// Admin\LeadController::store()/update() and Matchmaker\ClientController::
// update(). Previously called PushNotificationService directly, bypassing
// the database channel every other counselor notification uses — meaning
// this specific event never showed up in the in-app notification inbox or
// counted toward its unread badge. Matches the same pattern as the rest.
class MatchmakerLeadAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Client Assigned — Sallaamti Matchmaker')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->lead->name} has been assigned to you.")
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "👤 {$this->lead->name} has been assigned to you.",
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'New client assigned',
            'body' => "{$this->lead->name} has been assigned to you.",
            'data' => ['type' => 'lead_assigned', 'lead_id' => (string) $this->lead->id],
        ];
    }
}
