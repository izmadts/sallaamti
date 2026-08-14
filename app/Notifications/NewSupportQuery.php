<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportQuery extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Support Query — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A new support query has been submitted and needs attention.')
            ->line('Category: ' . ucfirst($this->query->category))
            ->line('Subject: ' . $this->query->subject)
            ->action('View Query', route('admin.support.show', $this->query));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => '🆘 New support query: ' . $this->query->subject,
            'url' => route('admin.support.show', $this->query),
        ];
    }
   
}
