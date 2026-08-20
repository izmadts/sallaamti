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
            // Subscribers already have their own token-based unsubscribe
            // flow (see SubscriberController) — reuse it rather than
            // routing them through the User-specific signed URL, since a
            // subscriber-sourced recipient has no user_id at all.
            $unsubscribeUrl = $recipient->subscriber_id
                ? route('subscriber.unsubscribe', $recipient->subscriber->unsubscribe_token)
                : URL::signedRoute('newsletter.unsubscribe', ['user' => $recipient->user_id]);

            // {{name}} is a plain runtime string token in the stored campaign
            // text, not Blade markup — safe to swap in per-recipient here,
            // no risk of colliding with Blade's own compiler.
            $name = $recipient->recipientName();
            $subject = str_replace('{{name}}', $name, $bulkMessage->subject ?? '');
            $body = str_replace('{{name}}', $name, $bulkMessage->body ?? '');

            Mail::to($recipient->channel_address)->send(
                new AdminNewsletter($subject, $body, $unsubscribeUrl)
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
