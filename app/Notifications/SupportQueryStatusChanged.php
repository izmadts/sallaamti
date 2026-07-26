<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportQueryStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SupportQuery $query, public string $newStatus) 
    {
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $labels = [
            'assigned' => 'assigned to a counselor',
            'in_progress' => 'being reviewed',
            'resolved' => 'resolved',
            'closed' => 'closed',
        ];
        return [
            'message' => '🔔 Your query "' . $this->query->subject . '" is now ' . ($labels[$this->newStatus] ?? $this->newStatus),
            'url' => route('support.show', $this->query),
        ];
    }
}
