<?php

namespace App\Notifications;

use App\Models\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// To every admin, once, the moment PublishCommunityPostToSocialJob detects
// an auth-type failure and flips a SocialAccount's status away from
// 'connected' (see that job's markAuthFailure()). Before this, a dead
// token (expired, revoked, re-authorized elsewhere) just failed every
// single post to that platform silently forever — nothing ever told anyone
// to go reconnect it, and the status column sat at 'connected' the whole
// time, so the admin integrations page kept claiming it was fine.
class SocialAccountNeedsReconnect extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SocialAccount $account, public string $reason) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(ucfirst($this->account->platform) . ' needs reconnecting — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('Posting to ' . ucfirst($this->account->platform) . ' (' . $this->account->display_name . ') has started failing:')
            ->line('"' . $this->reason . '"')
            ->line('This usually means the connection has expired or was revoked — every Community Post picked for this platform will keep failing silently until it\'s reconnected.')
            ->action('Reconnect ' . ucfirst($this->account->platform), route('admin.integrations.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '⚠️ ' . ucfirst($this->account->platform) . ' needs reconnecting — posts have started failing.',
            'platform' => $this->account->platform,
            'url' => route('admin.integrations.index'),
        ];
    }
}
