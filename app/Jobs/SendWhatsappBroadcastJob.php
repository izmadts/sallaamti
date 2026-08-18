<?php

namespace App\Jobs;

use App\Models\CommunityPost;
use App\Models\SocialAccount;
use App\Models\Setting;
use App\Models\User;
use App\Services\WhatsappNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Notifies opted-in members by WhatsApp when a Community Post publishes —
// deliberately a broadcast to individual users, not a post to a public feed
// (WhatsApp has no such surface; see WhatsappNotifier). Dispatched from
// CommunityPostPublisher alongside the per-platform social fan-out, but
// only when whatsapp_notifications_enabled is on and an account is
// connected — both false by default, so this does nothing until an admin
// explicitly turns it on.
class SendWhatsappBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(private readonly CommunityPost $post)
    {
    }

    public function handle(WhatsappNotifier $notifier): void
    {
        $account = SocialAccount::active('whatsapp');
        if (!$account) {
            return;
        }

        $templateName = Setting::get('whatsapp_template_name');
        if (!$templateName) {
            \Log::warning('SendWhatsappBroadcastJob: no whatsapp_template_name configured — skipping broadcast for post #' . $this->post->id);
            return;
        }

        $recipients = User::where('whatsapp_notify_opt_in', true)
            ->whereNotNull('phone')
            ->pluck('phone');

        foreach ($recipients as $phone) {
            // One recipient's failure (bad number, not yet in a valid
            // session, template mismatch) must not stop the rest of the
            // broadcast — log and move on rather than let the whole job
            // fail and retry from the top.
            try {
                $result = $notifier->sendTemplate($account, $phone, $templateName, [$this->post->title]);

                if (!$result['success']) {
                    \Log::warning("SendWhatsappBroadcastJob: send to {$phone} failed — {$result['error']}");
                }
            } catch (\Throwable $e) {
                \Log::warning("SendWhatsappBroadcastJob: send to {$phone} threw — " . $e->getMessage());
            }
        }
    }
}
