<?php

namespace App\Notifications;

use App\Models\AdminMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

// To every admin, when a member sends a support message. Email only fires
// for that member's very first message (see $isFirstMessage) — otherwise
// every reply in an ongoing back-and-forth would flood the admin inbox;
// the always-on 'database' channel (admin panel's unread badge) is enough
// to surface every later message without emailing for each one.
class NewAdminMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AdminMessage $message, public bool $isFirstMessage) {}

    public function via($notifiable): array
    {
        return $this->isFirstMessage ? ['database', 'mail'] : ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Member Message — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line($this->message->user->name . ' started a new conversation:')
            ->line('"' . $this->message->body . '"')
            ->action('Reply', route('admin.support-messages.show', $this->message->user_id));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💬 ' . $this->message->user->name . ': ' . Str::limit($this->message->body, 80),
            'user_id' => $this->message->user_id,
            'url' => route('admin.support-messages.show', $this->message->user_id),
        ];
    }
}
