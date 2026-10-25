<?php

namespace App\Notifications;

use App\Models\LeadMessageReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Mirrors NewNikahReportFiled, for the NC<->client chat instead of
// member-to-member. Admin-visible only, no automated consequence (see
// LeadMessageReport's own class comment).
class NewLeadMessageReportFiled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeadMessageReport $report) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Message Report — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A message in a Nikah Counselor <-> client conversation was reported and needs review.')
            ->line('**Reporter:** ' . $this->report->reporter->name)
            ->line('**Reported:** ' . $this->report->reported->name)
            ->line('**Reason:** ' . $this->report->reason)
            ->action('Review Report', route('admin.leads.message-reports.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🚩 ' . $this->report->reporter->name . ' reported a message from ' . $this->report->reported->name . '.',
            'url' => route('admin.leads.message-reports.index'),
        ];
    }
}
