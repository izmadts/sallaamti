<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NikahBlock;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use App\Notifications\NikahInterestAccepted;
use App\Notifications\NikahInterestDeclined;
use App\Notifications\NikahInterestReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class NikahInterestController extends Controller
{
    private function guardAgainstBruteForce(string $key): void
    {
        abort_if(
            RateLimiter::tooManyAttempts($key, 20),
            429,
            'Too many interest requests sent. Please wait before sending more.'
        );
        RateLimiter::hit($key, 3600);
    }

    public function send(Request $request, NikahProfile $profile): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;

        if (!$myProfile) {
            return response()->json(['code' => 'no_profile', 'message' => 'Create your Nikah profile first.'], 409);
        }

        $this->guardAgainstBruteForce('nikah-interest-send:' . $request->user()->id);

        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $wasDeclined = NikahInterest::where('sender_profile_id', $myProfile->id)
            ->where('receiver_profile_id', $profile->id)
            ->where('status', 'declined')
            ->exists();

        if ($wasDeclined) {
            return response()->json(['message' => "This person already declined your interest — you can't resend it."], 409);
        }

        $interest = NikahInterest::firstOrCreate([
            'sender_profile_id' => $myProfile->id,
            'receiver_profile_id' => $profile->id,
        ]);
        // firstOrCreate()'s in-memory model doesn't reflect the DB's own
        // default for columns not in the insert array (e.g. status) —
        // refresh so the response matches what a subsequent GET would show.
        $interest->refresh();

        try {
            $profile->user->notify(new NikahInterestReceived($interest));
        } catch (\Throwable $e) {
            Log::error('NikahInterestReceived notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Interest sent! You will be notified if accepted.', 'status' => $interest->status]);
    }

    public function index(Request $request): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;

        if (!$myProfile) {
            return response()->json(['code' => 'no_profile', 'message' => 'Create your Nikah profile first.'], 409);
        }

        $received = $myProfile->receivedInterests()->with('sender.user')->latest()->get();
        $sent = $myProfile->sentInterests()->with('receiver.user')->latest()->get();

        return response()->json([
            'received' => $received->map(fn ($i) => $this->interestPayload($i, $i->sender)),
            'sent' => $sent->map(fn ($i) => $this->interestPayload($i, $i->receiver)),
        ]);
    }

    public function accept(Request $request, NikahInterest $interest): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'accepted', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestAccepted($interest));
        } catch (\Throwable $e) {
            Log::error('NikahInterestAccepted notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Interest accepted! Contact details are now visible to both sides.', 'status' => 'accepted']);
    }

    public function decline(Request $request, NikahInterest $interest): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'declined', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestDeclined($interest));
        } catch (\Throwable $e) {
            Log::error('NikahInterestDeclined notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Interest declined.', 'status' => 'declined']);
    }

    private function interestPayload(NikahInterest $interest, NikahProfile $otherProfile): array
    {
        return [
            'interest_id' => $interest->id,
            'status' => $interest->status,
            'created_at' => $interest->created_at->toIso8601String(),
            'profile' => [
                'id' => $otherProfile->id,
                'name' => $interest->status === 'accepted' ? $otherProfile->user->name : null,
                'age' => $otherProfile->age,
                'city' => $otherProfile->city,
                'profession' => $otherProfile->profession,
                'photo_url' => $otherProfile->photo ? route('api.v1.nikah.file', [$otherProfile, 'photo']) : null,
            ],
        ];
    }
}
