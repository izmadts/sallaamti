<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewNikahPaymentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    // Takes the ID, not the NikahProfile model — this is queued (Sentry/
    // failed_jobs showed 30 of these failing with ModelNotFoundException).
    // A typed Eloquent property gets restored via SerializesModels when the
    // job finally runs, which on this host's cron-driven queue (processed
    // every 5 minutes, not instantly) can be minutes after the profile was
    // submitted — if it's deleted in that window, restoring the model
    // throws and the whole notification silently fails forever. Looking it
    // up fresh in via()/toMail()/toArray() lets a since-deleted profile
    // just skip sending instead of crashing the job.
    public function __construct(public int $profileId) {}

    public function via($notifiable): array
    {
        return $this->profile() ? ['database', 'mail'] : [];
    }

    public function toMail($notifiable): MailMessage
    {
        $profile = $this->profile();

        return (new MailMessage)
            ->subject('New Nikah Payment Proof Submitted — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A Nikah verification fee payment proof was submitted and needs confirmation.')
            ->line('**Member:** ' . $profile->user->name)
            ->line('**Amount:** Rs. ' . number_format($profile->payment_amount))
            ->line('**Method:** ' . ucfirst(str_replace('_', ' ', $profile->payment_method)))
            ->action('Review Payment', route('admin.nikah.payments'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💳 New Nikah payment proof from ' . $this->profile()->user->name . ' — needs confirmation.',
            'url' => route('admin.nikah.payments'),
        ];
    }

    private function profile(): ?NikahProfile
    {
        return NikahProfile::with('user')->find($this->profileId);
    }
}
