<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        $weekly = CounselorAvailability::where('counselor_id', Auth::id())
            ->whereNull('specific_date')
            ->orderBy('day_of_week')
            ->get();

        $overrides = CounselorAvailability::where('counselor_id', Auth::id())
            ->whereNotNull('specific_date')
            ->where('specific_date', '>=', now()->toDateString())
            ->orderBy('specific_date')
            ->get();

        return view('counselor.availability.index', compact('weekly', 'overrides'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6', 'required_without:specific_date'],
            'specific_date' => ['nullable', 'date', 'after_or_equal:today', 'required_without:day_of_week'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'min:10', 'max:180'],
        ]);

        $validated['counselor_id'] = Auth::id();
        $validated['is_active'] = true;

        CounselorAvailability::create($validated);

        return back()->with('status', 'Availability added.');
    }

    public function destroy(CounselorAvailability $availability)
    {
        abort_unless($availability->counselor_id === Auth::id(), 403);

        $availability->delete();

        return back()->with('status', 'Availability removed.');
    }
}
