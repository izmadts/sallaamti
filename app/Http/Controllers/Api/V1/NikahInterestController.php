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
            return response()->json(['code' => 'no_profile', 'message' => __('db.Create your Nikah profile first.')], 409);
        }

        if (!$myProfile->canInteract()) {
            return response()->json(['code' => 'not_verified', 'message' => __('db.Your profile must be fully verified and paid before you can send interest to others.')], 403);
        }

        $this->guardAgainstBruteForce('nikah-interest-send:' . $request->user()->id);

        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $wasDeclined = NikahInterest::where('sender_profile_id', $myProfile->id)
            ->where('receiver_profile_id', $profile->id)
            ->where('status', 'declined')
            ->exists();

        if ($wasDeclined) {
            return response()->json(['message' => __('db.This person already declined your interest — you can\'t resend it.')], 409);
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

        return response()->json(['message' => __('db.Interest sent! You will be notified if accepted.'), 'status' => $interest->status]);
    }

    public function index(Request $request): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;

        if (!$myProfile) {
            return response()->json(['code' => 'no_profile', 'message' => __('db.Create your Nikah profile first.')], 409);
        }

        $received = $myProfile->receivedInterests()->with('sender.user')->latest()->get();
        $sent = $myProfile->sentInterests()->with('receiver.user')->latest()->get();

        return response()->json([
            'received' => $received->map(fn ($i) => $this->interestPayload($i, $i->sender, $myProfile)),
            'sent' => $sent->map(fn ($i) => $this->interestPayload($i, $i->receiver, $myProfile)),
        ]);
    }

    public function accept(Request $request, NikahInterest $interest): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile && (int) $interest->receiver_profile_id === (int) $myProfile->id, 403);

        if (!$myProfile->canInteract()) {
            return response()->json(['code' => 'not_verified', 'message' => __('db.Your profile must be fully verified and paid before you can accept interests.')], 403);
        }

        $interest->update(['status' => 'accepted', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestAccepted($interest));
        } catch (\Throwable $e) {
            Log::error('NikahInterestAccepted notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => __('db.Interest accepted! Contact details are now visible to both sides.'), 'status' => 'accepted']);
    }

    public function decline(Request $request, NikahInterest $interest): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile && (int) $interest->receiver_profile_id === (int) $myProfile->id, 403);

        $interest->update(['status' => 'declined', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestDeclined($interest));
        } catch (\Throwable $e) {
            Log::error('NikahInterestDeclined notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => __('db.Interest declined.'), 'status' => 'declined']);
    }

    // Only the sender, and only while still pending — see the web
    // controller's cancel() for why this deletes rather than soft-cancels.
    public function cancel(Request $request, NikahInterest $interest): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile && (int) $interest->sender_profile_id === (int) $myProfile->id, 403);

        if ($interest->status !== 'pending') {
            return response()->json(['message' => __('db.This interest has already been responded to and can\'t be cancelled.')], 409);
        }

        $interest->delete();

        return response()->json(['message' => __('db.Interest request cancelled.')]);
    }

    // $otherProfile can legitimately be null — the sender/receiver's
    // NikahProfile row went away from under an interest row that's still
    // here (e.g. data cleaned up outside the normal FK cascade). Used to
    // crash the ENTIRE index() response with a 500 the moment one bad row
    // showed up in the ->map(), taking out the whole Received/Sent list
    // for the user, not just that one row — hence the explicit null check
    // instead of relying on the type hint. The mobile client renders this
    // (and a blocked pair) as a disabled "no longer available" card rather
    // than a normal, tappable one.
    private function interestPayload(NikahInterest $interest, ?NikahProfile $otherProfile, NikahProfile $myProfile): array
    {
        if (!$otherProfile) {
            return [
                'interest_id' => $interest->id,
                'status' => $interest->status,
                'created_at' => $interest->created_at->toIso8601String(),
                'blocked' => false,
                'unavailable' => true,
                'profile' => null,
            ];
        }

        return [
            'interest_id' => $interest->id,
            'status' => $interest->status,
            'created_at' => $interest->created_at->toIso8601String(),
            'blocked' => NikahBlock::existsBetween($myProfile->id, $otherProfile->id),
            'unavailable' => false,
            'profile' => [
                'id' => $otherProfile->id,
                'name' => $interest->status === 'accepted' ? $otherProfile->user?->name : null,
                'age' => $otherProfile->age,
                'city' => $otherProfile->city,
                'profession' => $otherProfile->profession,
                'photo_url' => $otherProfile->photo ? route('api.v1.nikah.file', [$otherProfile, 'photo']) : null,
            ],
        ];
    }
}
