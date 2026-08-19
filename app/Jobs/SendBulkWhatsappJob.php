<?php

namespace App\Jobs;

use App\Models\BulkMessageRecipient;
use App\Models\SocialAccount;
use App\Services\WhatsappNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

// One job per recipient, same rate-limiting pattern as SendBulkEmailJob but
// against the 'bulk-whatsapp' limiter — WhatsApp Cloud API also enforces its
// own per-second send-rate tiers.
class SendBulkWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly BulkMessageRecipient $recipient)
    {
    }

    public function middleware(): array
    {
        return [new RateLimited('bulk-whatsapp')];
    }

    public function handle(WhatsappNotifier $notifier): void
    {
        $recipient = $this->recipient;
        $bulkMessage = $recipient->bulkMessage;

        $account = SocialAccount::active('whatsapp');

        if (!$account) {
            $recipient->update(['status' => 'failed', 'error' => 'No connected WhatsApp Business account.']);
            $bulkMessage->increment('failed_count');
            $this->maybeMarkCompleted($bulkMessage);
            return;
        }

        try {
            $result = $notifier->sendTemplate(
                $account,
                $recipient->channel_address,
                $bulkMessage->whatsapp_template_name,
                $bulkMessage->whatsapp_template_params ?? []
            );

            if ($result['success']) {
                $recipient->update(['status' => 'sent', 'sent_at' => now()]);
                $bulkMessage->increment('sent_count');
            } else {
                $recipient->update(['status' => 'failed', 'error' => $result['error']]);
                $bulkMessage->increment('failed_count');
            }
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
