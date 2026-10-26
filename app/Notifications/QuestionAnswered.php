<?php

namespace App\Notifications;

use App\Models\Question;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuestionAnswered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Question $question) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush', FcmChannel::class];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Your question was answered')
            ->body(Str::limit($this->question->question, 80));
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Your question was answered',
            'body' => Str::limit($this->question->question, 80),
            'data' => ['type' => 'question_answered', 'question_id' => (string) $this->question->id],
            'app' => 'sallaamti_app',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Your question was answered: "' . Str::limit($this->question->question, 80) . '"',
            'question_id' => $this->question->id,
        ];
    }
}
