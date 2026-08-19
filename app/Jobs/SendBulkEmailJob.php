<?php

namespace App\Jobs;

use App\Mail\AdminNewsletter;
use App\Models\BulkMessageRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

// One job per recipient, rate-limited via the 'bulk-email' limiter (see
// AppServiceProvider) so a campaign of any size automatically stays under
// Gmail SMTP's daily sending cap instead of trying to blast everything at
// once — no manual delay math needed.
class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly BulkMessageRecipient $recipient)
    {
    }

    public function middleware(): array
    {
        return [new RateLimited('bulk-email')];
    }

    public function handle(): void
    {
        $recipient = $this->recipient;
        $bulkMessage = $recipient->bulkMessage;

        try {
            $unsubscribeUrl = URL::signedRoute('newsletter.unsubscribe', ['user' => $recipient->user_id]);

            Mail::to($recipient->channel_address)->send(
                new AdminNewsletter($bulkMessage->subject ?? '', $bulkMessage->body ?? '', $unsubscribeUrl)
            );

            $recipient->update(['status' => 'sent', 'sent_at' => now()]);
            $bulkMessage->increment('sent_count');
        } catch (\Throwable $e) {
            $recipient->update(['status' => 'failed', 'error' => $e->getMessage()]);
            $bulkMessage->increment('failed_count');
        }

        $this->maybeMarkCompleted($bulkMessage);
    }

    private function maybeMarkCompleted($bulkMessage): void
    {
        $bulkMessage->refresh();

        if ($bulkMessage->sent_count + $bulkMessage->failed_count >= $bulkMessage->recipient_count) {
            $bulkMessage->update(['status' => 'completed']);
        }
    }
}
