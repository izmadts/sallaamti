<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\LibraryItemPublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Same fan-out pattern as NotifyMembersOfNewPostJob — dispatched once from
// Admin\LibraryItemController when a library item is (a) published and
// (b) the "Send notification to members" checkbox was on, for an item
// that hasn't been notified before (notified_at still null).
class NotifyMembersOfNewLibraryItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly int $libraryItemId)
    {
    }

    public function handle(): void
    {
        User::role('member')->chunkById(200, function ($members) {
            foreach ($members as $member) {
                try {
                    $member->notify(new LibraryItemPublished($this->libraryItemId));
                } catch (\Throwable $e) {
                    \Log::warning("NotifyMembersOfNewLibraryItemJob: notify user #{$member->id} failed — " . $e->getMessage());
                }
            }
        });
    }
}
