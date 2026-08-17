<?php

namespace App\Notifications\Channels;

use App\Models\PushSubscription;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWebPush')) {
            return;
        }

        $subscriptions = $notifiable->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        if (!config('services.webpush.public_key') || !config('services.webpush.private_key')) {
            \Log::warning('WebPushChannel: VAPID keys not configured, skipping push send.');

            return;
        }

        $message = $notification->toWebPush($notifiable);
        $payload = json_encode($message->toArray());

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);

        // Sent one at a time (not queueNotification()+flush()'s shared batch)
        // so one subscriber's malformed/corrupted keys — which only surface
        // as an error deep inside the send, not when queuing — can never
        // abort delivery to that same user's other devices.
        foreach ($subscriptions as $subscription) {
            try {
                $report = $webPush->sendOneNotification(
                    Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'publicKey' => $subscription->public_key,
                        'authToken' => $subscription->auth_token,
                        'contentEncoding' => $subscription->content_encoding ?? 'aes128gcm',
                    ]),
                    $payload
                );

                // A push service reporting the subscription is gone
                // (expired/unsubscribed on the browser side) means it will
                // never succeed again — prune it so this table doesn't
                // accumulate dead endpoints forever.
                if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint_hash', PushSubscription::hashEndpoint($subscription->endpoint))->delete();
                }
            } catch (\Throwable $e) {
                // Deliberately not deleting here — this could be a transient
                // network failure to the push service, not proof the
                // subscription itself is dead. Only an authoritative "gone"
                // response above (isSubscriptionExpired()) does that.
                \Log::warning('WebPushChannel: failed to send to one subscription: ' . $e->getMessage());
            }
        }
    }
}
