<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\NikahContactRequest;
use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// A matchmaker can browse every profile to help arrange matches, but never
// sees identity-revealing fields (CNIC, photo) or a way to contact either
// party directly — they request contact details from admin per profile, and
// only admin decides whether to grant it. Keeps the matchmaker facilitating
// rather than directly involved with both parties.
class NikahBrowseController extends Controller
{
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

        return view('matchmaker.nikah.show', compact('profile', 'existingRequest'));
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
}
