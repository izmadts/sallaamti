<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingBooking;
use App\Models\QueryResponse;
use App\Models\User;
use App\Notifications\CounselingBookingCancelled;
use App\Notifications\CounselingBookingReassigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselingBookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = CounselingBooking::with(['member', 'counselor', 'supportQuery'])->orderByDesc('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('counselor_id')) {
            $request->counselor_id === 'unassigned'
                ? $query->whereNull('counselor_id')
                : $query->where('counselor_id', $request->counselor_id);
        }
        if ($request->boolean('urgent_only')) {
            $query->whereHas('supportQuery', fn ($q) => $q->where('priority', 'high'));
        }

        $bookings = $query->paginate(20)->withQueryString();
        // Admin can assign any counselor — or themselves, or any other
        // admin, to personally take a session ("admin by self can attend").
        $counselors = User::role(['counselor', 'admin'])->orderBy('name')->get();

        return view('admin.counseling-bookings.index', compact('bookings', 'counselors'));
    }

    public function show(CounselingBooking $booking)
    {
        $booking->load(['member', 'counselor', 'supportQuery.responses.responder']);
        $counselors = User::role(['counselor', 'admin'])->orderBy('name')->get();

        return view('admin.counseling-bookings.show', compact('booking', 'counselors'));
    }

    public function reassign(Request $request, CounselingBooking $booking)
    {
        $request->validate(['counselor_id' => ['required', 'exists:users,id']]);

        $conflict = CounselingBooking::where('counselor_id', $request->counselor_id)
            ->where('scheduled_at', $booking->scheduled_at)
            ->whereIn('status', ['requested', 'confirmed'])
            ->where('id', '!=', $booking->id)
            ->exists();

        if ($conflict) {
            return back()->with('error', 'That counselor already has a session booked at this time — pick a different counselor or reschedule first.');
        }

        $booking->update(['counselor_id' => $request->counselor_id, 'status' => 'requested', 'confirmed_at' => null]);
        $booking->refresh();

        $booking->counselor?->notify(new CounselingBookingReassigned($booking, forCounselor: true));
        $booking->member?->notify(new CounselingBookingReassigned($booking, forCounselor: false));

        return back()->with('status', 'Booking reassigned — both the counselor and member have been notified. The counselor will need to re-confirm the session.');
    }

    public function cancel(Request $request, CounselingBooking $booking)
    {
        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason', 'Cancelled by admin.'),
            'cancelled_at' => now(),
        ]);

        $booking->member?->notify(new CounselingBookingCancelled($booking));

        return back()->with('status', 'Booking cancelled and the member has been notified.');
    }

    public function reply(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->support_query_id, 404);

        $validated = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        QueryResponse::create([
            'support_query_id' => $booking->support_query_id,
            'responder_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        return back()->with('status', 'Message sent.');
    }
}
