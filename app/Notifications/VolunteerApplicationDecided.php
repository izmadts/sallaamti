<?php

namespace App\Notifications;

use App\Models\VolunteerApplication;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

// The approve/reject email already goes out separately via
// Mail::send(VolunteerApproved|VolunteerApplicationDecision) in
// VolunteerAdminController — this only adds the in-app/push side for a
// registered applicant, so 'mail' is deliberately left out of via() to
// avoid sending the decision twice.
class VolunteerApplicationDecided extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VolunteerApplication $application, public bool $approved) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => $this->approved ? '🎉 Volunteer application approved!' : 'Volunteer application update',
            'body' => $this->approved
                ? 'Welcome aboard! Your Volunteer ID card is ready to download.'
                : "We're not able to move forward with your volunteer application right now.",
            'data' => ['type' => 'volunteer_decision', 'application_id' => (string) $this->application->id],
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->approved
                ? '🎉 Your volunteer application was approved — your ID card is ready.'
                : 'Your volunteer application was not approved this time.',
            'application_id' => $this->application->id,
            'approved' => $this->approved,
        ];
    }
}
