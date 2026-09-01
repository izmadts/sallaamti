<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;

// Sends a push to every device a user is signed into the Nikah Counselor
// app on. Silently a no-op if the service-account key isn't present yet
// (local dev, or before the credentials file has been dropped in) — a
// missing push must never break the action that triggered it (assigning a
// lead, confirming a payment, etc).
class PushNotificationService
{
    /**
     * $app narrows delivery to one app's registered devices ('sallaamti_app'
     * or 'nikah_counselor', see DeviceToken::$app) — left null (send to every
     * device this user has, regardless of which app registered it) for
     * existing callers. Matters once a notification's `data` payload is
     * meaningful to only one app's routing (e.g. a Quran Live class-link
     * ping means nothing to the counselor app) — the rare account that's
     * both a member and a counselor shouldn't get a push the other app
     * can't make sense of.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], ?string $app = null): void
    {
        $tokens = DeviceToken::where('user_id', $user->id)
            ->when($app, fn ($query) => $query->where('app', $app))
            ->pluck('token', 'id');
        if ($tokens->isEmpty()) {
            return;
        }

        $messaging = $this->messaging();
        if (!$messaging) {
            return;
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $report = $messaging->sendMulticast($message, $tokens->values()->all());
            $this->pruneInvalidTokens($tokens, $report);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // FCM tells us exactly which tokens are no longer valid (app
    // uninstalled, token rotated without us hearing about it yet) — drop
    // them so future sends don't keep paying for a doomed request.
    private function pruneInvalidTokens($tokens, MulticastSendReport $report): void
    {
        foreach ($report->invalidTokens() as $invalidToken) {
            DeviceToken::where('token', $invalidToken)->delete();
        }
    }

    private function messaging(): ?\Kreait\Firebase\Contract\Messaging
    {
        $path = config('services.firebase.credentials');

        if (!$path || !file_exists($path)) {
            Log::info('Push notification skipped — no Firebase credentials configured at ' . $path);
            return null;
        }

        return (new Factory)->withServiceAccount($path)->createMessaging();
    }
}
