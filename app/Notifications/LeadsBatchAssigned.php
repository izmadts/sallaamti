<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

// Fires once per batch from Admin\LeadController::bulkAssign() — not once
// per lead, so a counselor given 20 leads in one sweep gets a single push,
// not 20. Only ever a city label + a count, never lead references, so
// there's nothing here that can go stale if a lead is edited afterward.
// Explicitly scoped to the Nikah Counselor app (unlike MatchmakerLeadAssigned,
// which is left meaningful to either app) — this is purely an NC-app workflow.
class LeadsBatchAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $city, public int $count) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "👥 آپ کے شہر ({$this->city}) کی {$this->count} نئی لیڈز آپ کو دی گئی ہیں۔",
            'url' => route('matchmaker.clients.index'),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'نئی لیڈز تفویض ہوئیں',
            'body' => "آپ کے شہر کی {$this->count} نئی لیڈز آپ کو دی گئی ہیں۔",
            'data' => ['type' => 'leads_batch_assigned', 'count' => (string) $this->count],
            'app' => 'nikah_counselor',
        ];
    }
}
