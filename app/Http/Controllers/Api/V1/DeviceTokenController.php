<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Registers/refreshes the FCM token for the device a Sallaamti member is
// signed in on, mirroring Api\V1\Matchmaker\DeviceTokenController's pattern
// for the Nikah Counselor app. DeviceToken and PushNotificationService are
// already generic (queried by user_id only, no app filter), so the only
// difference from the counselor version is the 'app' tag stored alongside
// the token — that's what tells the two apps' tokens apart, if it ever
// matters, without changing how sending works.
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
                'user_id' => $request->user()->id,
                'platform' => $validated['platform'] ?? null,
                'app' => 'sallaamti_app',
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        DeviceToken::where('user_id', $request->user()->id)->where('token', $request->string('token'))->delete();

        return response()->json(['status' => 'ok']);
    }
}
