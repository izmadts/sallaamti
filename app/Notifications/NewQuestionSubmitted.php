<?php

namespace App\Notifications;

use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewQuestionSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Question $question) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Member Question Submitted — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A member asked a new question in "Ap k Sawalat" and it needs an answer.')
            ->line('**Question:** ' . $this->question->question)
            ->action('Answer It', route('admin.questions.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '❓ New question: "' . Str::limit($this->question->question, 80) . '"',
            'url' => route('admin.questions.index'),
        ];
    }
}
