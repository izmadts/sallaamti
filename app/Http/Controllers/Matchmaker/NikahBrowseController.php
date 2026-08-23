<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Concerns\SubmitsNikahPayment;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahContactRequest;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// A matchmaker can browse every profile to help arrange matches, but never
// sees identity-revealing fields (CNIC, photo) or a way to contact either
// party directly — they request contact details from admin per profile, and
// only admin decides whether to grant it. Keeps the matchmaker facilitating
// rather than directly involved with both parties.
class NikahBrowseController extends Controller
{
    use SubmitsNikahPayment;


    public function index(Request $request)
    {
        $query = NikahProfile::with('user')->matchmakerVisible();

        if ($request->filled('gender')) {
            $query->whereHas('user', fn ($q) => $q->where('gender', $request->gender));
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('sect')) {
            $query->where('sect', 'like', '%' . $request->sect . '%');
        }
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }
        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->min_age);
        }
        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->max_age);
        }

        $profiles = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('matchmaker.nikah.index', compact('profiles'));
    }

    // A matchmaker previously had no way to see their own request history
    // in one place — only a per-profile status on show() above, or the
    // three-number summary on their dashboard. This is the "My Requests"
    // list that summary was missing a link to.
    public function myRequests()
    {
        $requests = NikahContactRequest::with('profile.user')
            ->where('requested_by', Auth::id())
            ->latest()
            ->paginate(20);

        return view('matchmaker.nikah.requests', compact('requests'));
    }

    public function show(NikahProfile $profile)
    {
        $existingRequest = NikahContactRequest::where('nikah_profile_id', $profile->id)
            ->where('requested_by', Auth::id())
            ->latest()
            ->first();

        $canSubmitPayment = Auth::user()->hasRole('admin')
            || $profile->created_by === Auth::id()
            || Lead::where('nikah_profile_id', $profile->id)->where('assigned_to', Auth::id())->exists();

        return view('matchmaker.nikah.show', compact('profile', 'existingRequest', 'canSubmitPayment'));
    }

    public function requestContact(NikahProfile $profile)
    {
        $pending = NikahContactRequest::where('nikah_profile_id', $profile->id)
            ->where('requested_by', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pending) {
            return back()->with('error', 'You already have a request on file for this profile.');
        }

        NikahContactRequest::create([
            'nikah_profile_id' => $profile->id,
            'requested_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('status', 'Contact request sent to admin for review.');
    }

    // Lets a matchmaker relay a client's payment receipt directly — same
    // outcome as the client submitting it themselves (status → 'submitted',
    // straight into Admin\NikahPaymentAdminController's normal review
    // queue, never auto-confirmed). Reachable from this profile's own page
    // and from the client's page in the Match Maker Desk (both point here).
    public function submitPayment(Request $request, NikahProfile $profile)
    {
        $this->authorizePaymentSubmission($profile);

        abort_if($profile->payment_status === 'confirmed', 422, 'Payment is already confirmed for this profile.');

        $fee = $profile->applicableVerificationFee();
        abort_if($fee <= 0, 422, 'No verification fee applies to this profile — nothing to submit.');

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,bank_transfer'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $screenshotPath = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');

        $this->recordNikahPaymentSubmission($profile, $validated['payment_method'], $validated['payment_reference'] ?? null, $screenshotPath);

        $lead = Lead::where('nikah_profile_id', $profile->id)->first();
        if ($lead) {
            MatchmakingTimelineEvent::log($lead, $profile, 'payment_submitted', 'Payment proof submitted for admin review.');
        }

        return back()->with('status', 'Payment proof submitted — our team will confirm it shortly.');
    }

    // Only a matchmaker who actually walked this person in, or who is the
    // assigned matchmaker for a client this profile is linked to, can
    // submit a payment claim on their behalf — not any matchmaker for any
    // profile in the system.
    private function authorizePaymentSubmission(NikahProfile $profile): void
    {
        if (auth()->user()->hasRole('admin')) {
            return;
        }

        $isCreator = $profile->created_by === auth()->id();
        $isAssignedViaLead = Lead::where('nikah_profile_id', $profile->id)->where('assigned_to', auth()->id())->exists();

        abort_unless($isCreator || $isAssignedViaLead, 403, 'You can only submit payment for clients you registered or are assigned to.');
    }
}
