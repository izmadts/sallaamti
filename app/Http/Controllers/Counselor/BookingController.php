<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingBooking;
use App\Models\QueryResponse;
use App\Notifications\CounselingBookingCancelled;
use App\Notifications\CounselingBookingConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        // Urgent first, then soonest — a counselor triaging their queue
        // should see safety-flagged requests before anything else.
        $upcoming = CounselingBooking::where('counselor_id', Auth::id())
            ->whereIn('status', ['requested', 'confirmed'])
            ->with(['member', 'supportQuery'])
            ->get()
            ->sortByDesc(fn ($booking) => $booking->isUrgent())
            ->values();

        $past = CounselingBooking::where('counselor_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->with('member')
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('counselor.bookings.index', compact('upcoming', 'past'));
    }

    public function show(CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);
        $booking->load(['member', 'supportQuery.responses.responder']);

        return view('counselor.bookings.show', compact('booking'));
    }

    public function confirm(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if ($booking->status !== 'requested') {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — nothing to confirm.');
        }

        $validated = $request->validate(['meeting_link' => ['nullable', 'string', 'max:500']]);

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'meeting_link' => $validated['meeting_link'] ?: $booking->meeting_link,
        ]);
        $booking->member->notify(new CounselingBookingConfirmed($booking));

        return back()->with('status', 'Session confirmed.');
    }

    public function complete(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be marked complete.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'internal_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $validated['notes'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        return back()->with('status', 'Session marked as completed.');
    }

    public function cancel(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        $booking->member->notify(new CounselingBookingCancelled($booking));

        return back()->with('status', 'Session cancelled.');
    }

    public function markNoShow(CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be marked no-show.');
        }

        $booking->update(['status' => 'no_show']);

        return back()->with('status', 'Marked as no-show.');
    }

    // Lets the counselor ask a clarifying question (or the member add
    // detail) before the session, using the SupportQuery/QueryResponse
    // thread the booking already created but never surfaced anywhere.
    public function reply(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);
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
