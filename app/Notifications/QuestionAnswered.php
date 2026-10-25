<?php

namespace App\Notifications;

use App\Models\Question;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QuestionAnswered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Question $question) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Your question was answered')
            ->body(\Illuminate\Support\Str::limit($this->question->question, 80));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Your question was answered: "' . \Illuminate\Support\Str::limit($this->question->question, 80) . '"',
            'question_id' => $this->question->id,
        ];
    }
}
