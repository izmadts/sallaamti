<?php

namespace App\Notifications\Channels;

use App\Services\PushNotificationService;
use Illuminate\Notifications\Notification;

// A standard Laravel notification channel — add \App\Notifications\Channels\
// FcmChannel::class to any Notification's via() and implement toFcm() on it
// (return null to skip sending for that particular case, e.g. the admin leg
// of a dual-audience notification) to also push it to the Nikah Counselor
// app. Reuses whatever recipient the notification already targets, so every
// existing mail/database notification stays the single source of truth for
// "who gets told what" — this only adds a delivery channel.
class FcmChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $payload = $notification->toFcm($notifiable);
        if (!$payload) {
            return;
        }

        app(PushNotificationService::class)->sendToUser(
            $notifiable,
            $payload['title'],
            $payload['body'],
            $payload['data'] ?? []
        );
    }
}
