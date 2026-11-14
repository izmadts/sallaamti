<?php

namespace App\Notifications;

use App\Models\LibraryItem;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Messages\WebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

// One new free booklet/document on /library — sent once per item
// (LibraryItemController sets notified_at right after dispatching the job
// that sends this). Takes the ID, not the model — this is queued on a
// cron-driven worker that can run minutes after admin saves the item, and
// a since-deleted item would otherwise crash the whole fan-out job (same
// fix as NewNikahPaymentSubmitted this session).
class LibraryItemPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $libraryItemId) {}

    public function via($notifiable): array
    {
        return $this->item() ? ['database', 'webpush', FcmChannel::class] : [];
    }

    public function toWebPush($notifiable): WebPushMessage
    {
        $item = $this->item();

        return (new WebPushMessage)
            ->title('📚 نئی کتاب: ' . $item->title)
            ->body(Str::limit($item->description ?: $item->title, 120))
            ->url(route('library.show', $item));
    }

    public function toFcm($notifiable): array
    {
        $item = $this->item();

        return [
            'title' => '📚 نئی کتاب: ' . $item->title,
            'body' => Str::limit($item->description ?: $item->title, 120),
            'data' => ['type' => 'library_item', 'library_item_slug' => $item->slug],
            'app' => 'sallaamti_app',
        ];
    }

    public function toArray($notifiable): array
    {
        $item = $this->item();

        return [
            'type' => 'library_item',
            'message' => '📚 نئی کتاب: ' . $item->title,
            'library_item_slug' => $item->slug,
            'url' => route('library.show', $item),
        ];
    }

    private function item(): ?LibraryItem
    {
        return LibraryItem::find($this->libraryItemId);
    }
}
