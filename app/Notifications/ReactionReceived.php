<?php

namespace App\Notifications;

use App\Models\Reaction;
use App\Models\User;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class ReactionReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Model $reactable, public User $reactor, public string $type) {}

    public function via($notifiable): array
    {
        return ['database', 'webpush'];
    }

    protected function label(): string
    {
        [$emoji, $label] = Reaction::TYPES[$this->type] ?? ['🤲', 'Ameen'];

        return "$emoji $label";
    }

    protected function subject(): string
    {
        return $this->reactable instanceof \App\Models\DuaRequest ? 'dua' : 'post';
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        return (new WebPushMessage)
            ->title("Someone reacted {$this->label()} to your {$this->subject()}")
            ->body($this->reactor->name . " reacted on Sallaamti.")
            ->url(route('wall.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->reactor->name . " reacted {$this->label()} to your {$this->subject()}.",
            'reactable_type' => get_class($this->reactable),
            'reactable_id' => $this->reactable->id,
            'url' => route('wall.index'),
        ];
    }
}
