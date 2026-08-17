<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['nullable', 'string'],
            'keys.auth' => ['nullable', 'string'],
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => PushSubscription::hashEndpoint($validated['endpoint'])],
            [
                'user_id' => Auth::id(),
                'endpoint' => $validated['endpoint'],
                'public_key' => $validated['keys']['p256dh'] ?? null,
                'auth_token' => $validated['keys']['auth'] ?? null,
                'content_encoding' => 'aes128gcm',
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => ['required', 'string']]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint_hash', PushSubscription::hashEndpoint($request->endpoint))
            ->delete();

        return response()->json(['success' => true]);
    }
}
