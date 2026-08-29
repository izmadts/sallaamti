<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Concerns\SendsNikahInterest;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use Illuminate\Http\JsonResponse;

// Mutual-interest inbox aggregated across every profile the matchmaker can
// legitimately act on behalf of: profiles they personally registered
// (created_by) and profiles linked to a Lead assigned to them — mirrors
// Matchmaker\NikahBrowseController::acceptInterestOnBehalf/declineInterestOnBehalf's
// authorizeActionOnBehalf() rule, but as one combined list instead of the
// web's per-profile view (matchmaker.nikah.show).
class InterestController extends Controller
{
    use SendsNikahInterest;

    private function actionableProfileIds(): array
    {
        $ownProfileIds = NikahProfile::where('created_by', auth()->id())->pluck('id');
        $assignedProfileIds = Lead::where('assigned_to', auth()->id())->whereNotNull('nikah_profile_id')->pluck('nikah_profile_id');

        return $ownProfileIds->merge($assignedProfileIds)->unique()->values()->all();
    }

    public function index(): JsonResponse
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $query = NikahInterest::where('status', 'pending')->with('sender.user', 'receiver.user');

        if (!$isAdmin) {
            $query->whereIn('receiver_profile_id', $this->actionableProfileIds());
        }

        $interests = $query->latest()->get();

        return response()->json([
            'interests' => $interests->map(fn ($i) => [
                'id' => $i->id,
                'created_at' => $i->created_at->toIso8601String(),
                'receiver' => [
                    'id' => $i->receiver->id,
                    'name' => $i->receiver->user?->name,
                ],
                'sender' => [
                    'id' => $i->sender->id,
                    'age' => $i->sender->age,
                    'city' => $i->sender->city,
                    'sect' => $i->sender->sect,
                ],
            ]),
        ]);
    }

    public function accept(NikahInterest $interest): JsonResponse
    {
        $this->authorizeActionOnBehalf($interest->receiver);

        abort_if($interest->status !== 'pending', 422, 'This interest has already been responded to.');
        abort_unless($interest->receiver->canInteract(), 422, "This client's own profile must be verified and payment confirmed before accepting interests.");

        $this->acceptNikahInterest($interest);

        return response()->json(['message' => __("db.Interest accepted on the client's behalf — contact details are now visible to both sides."), 'status' => 'accepted']);
    }

    public function decline(NikahInterest $interest): JsonResponse
    {
        $this->authorizeActionOnBehalf($interest->receiver);

        abort_if($interest->status !== 'pending', 422, 'This interest has already been responded to.');

        $this->declineNikahInterest($interest);

        return response()->json(['message' => __("db.Interest declined on the client's behalf."), 'status' => 'declined']);
    }

    private function authorizeActionOnBehalf(NikahProfile $profile): void
    {
        if (auth()->user()->hasRole('admin')) {
            return;
        }

        $isCreator = (int) $profile->created_by === (int) auth()->id();
        $isAssignedViaLead = Lead::where('nikah_profile_id', $profile->id)->where('assigned_to', auth()->id())->exists();

        abort_unless($isCreator || $isAssignedViaLead, 403, 'You can only act on behalf of clients you registered or are assigned to.');
    }
}
