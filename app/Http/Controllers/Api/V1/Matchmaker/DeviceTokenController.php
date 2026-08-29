<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Registers/refreshes the FCM token for the device the counselor is
// currently signed in on, so a push notification can reach them. The
// counselor app calls this once after login and again whenever Firebase
// hands it a rotated token. Actual sending is a separate piece (a
// PushNotificationService using a Firebase service-account key) — this
// endpoint only keeps the token store current.
class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'in:android,ios'],
        ]);

        DeviceToken::updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => auth()->id(),
                'platform' => $validated['platform'] ?? null,
                'app' => 'nikah_counselor',
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        DeviceToken::where('user_id', auth()->id())->where('token', $request->string('token'))->delete();

        return response()->json(['status' => 'ok']);
    }
}
