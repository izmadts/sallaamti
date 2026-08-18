<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Models\CounselingBooking;
use App\Models\CounselorAvailability;
use App\Models\SupportQuery;
use App\Models\User;
use App\Notifications\CounselingBookingRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CounselingBookingController extends Controller
{
    use HasWizardSteps;

    protected string $wizardKey = 'counseling_booking';

    protected array $wizardStepFields = [
        'category' => ['category'],
        'describe' => ['subject', 'description', 'is_anonymous'],
        'contact' => ['contact_method'],
        'counselor' => ['counselor_choice'],
        'slot' => ['counselor_id', 'scheduled_at'],
    ];

    protected array $stepTitles = [
        'category' => 'Concern',
        'describe' => 'Describe',
        'contact' => 'Contact Method',
        'counselor' => 'Counselor',
        'slot' => 'Time Slot',
    ];

    public function start()
    {
        $step = $this->firstIncompleteWizardStep();

        return $step
            ? redirect()->route('counseling.book.step', $step)
            : redirect()->route('counseling.book.review');
    }

    public function showStep(string $step)
    {
        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        $viewData = [
            'step' => $step,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $this->wizardStepData($step),
        ];

        if ($step === 'counselor') {
            $viewData['counselors'] = User::role('counselor')->orderBy('name')->get();
        }

        if ($step === 'slot') {
            $counselorData = $this->wizardStepData('counselor');
            $choice = $counselorData['counselor_choice'] ?? 'auto';
            $counselorIds = $choice === 'auto'
                ? User::role('counselor')->pluck('id')->all()
                : [(int) $choice];

            $date = request('date') ? Carbon::parse(request('date')) : Carbon::today();
            $viewData['date'] = $date;
            $viewData['slots'] = $this->availableSlots($counselorIds, $date);
        }

        return view("counseling.wizard.step-{$step}", $viewData);
    }

    public function saveStep(Request $request, string $step)
    {
        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        if ($step === 'category') {
            $validated = $request->validate([
                'category' => ['required', 'in:marital,parenting,financial,legal,spiritual,other'],
            ]);
        } elseif ($step === 'describe') {
            $validated = $request->validate([
                'subject' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'min:20', 'max:5000'],
                'is_anonymous' => ['nullable', 'boolean'],
            ]);
            $validated['is_anonymous'] = $request->boolean('is_anonymous');
        } elseif ($step === 'contact') {
            $validated = $request->validate([
                'contact_method' => ['required', 'in:phone,video,in_person,chat'],
            ]);
        } elseif ($step === 'counselor') {
            $validated = $request->validate([
                'counselor_choice' => ['required'],
            ]);
        } elseif ($step === 'slot') {
            $request->validate(['slot' => ['required', 'string']]);
            [$counselorId, $scheduledAt] = explode('|', $request->slot, 2);

            $counselorData = $this->wizardStepData('counselor');
            $choice = $counselorData['counselor_choice'] ?? 'auto';
            $allowedIds = $choice === 'auto' ? User::role('counselor')->pluck('id')->all() : [(int) $choice];

            abort_unless(in_array((int) $counselorId, $allowedIds), 422, 'Invalid counselor selection.');

            $date = Carbon::parse($scheduledAt);
            $stillAvailable = collect($this->availableSlots([(int) $counselorId], $date))
                ->contains(fn ($slot) => $slot['datetime']->eq($date) && $slot['counselor_id'] === (int) $counselorId);

            if (!$stillAvailable) {
                return back()->withErrors(['slot' => 'That slot was just taken — please pick another.']);
            }

            $validated = ['counselor_id' => (int) $counselorId, 'scheduled_at' => $date->toDateTimeString()];
        }

        $this->saveWizardStep($step, $validated);

        $steps = $this->wizardSteps();
        $nextIndex = array_search($step, $steps) + 1;

        return $nextIndex < count($steps)
            ? redirect()->route('counseling.book.step', $steps[$nextIndex])
            : redirect()->route('counseling.book.review');
    }

    public function review()
    {
        if ($incomplete = $this->firstIncompleteWizardStep()) {
            return redirect()->route('counseling.book.step', $incomplete);
        }

        $data = $this->wizardAllData();
        $counselor = !empty($data['counselor_id']) ? User::find($data['counselor_id']) : null;

        return view('counseling.wizard.review', [
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->stepTitles,
            'data' => $data,
            'counselor' => $counselor,
        ]);
    }

    public function finalize()
    {
        if ($this->firstIncompleteWizardStep()) {
            return redirect()->route('counseling.book.start');
        }

        $data = $this->wizardAllData();

        // The 'slot' wizard step already checked availability, but that
        // happened in an earlier request — the member can sit on the review
        // screen for a while before finalizing, and two members can race for
        // the same counselor/time in that window. Re-check inside a locked
        // transaction right before inserting: SELECT ... FOR UPDATE against
        // no matching row still takes a gap lock under MySQL's default
        // REPEATABLE READ isolation, so a concurrent finalize() for the same
        // slot blocks until this one commits, then correctly sees the
        // conflict instead of creating a duplicate booking.
        $booking = DB::transaction(function () use ($data) {
            $conflict = CounselingBooking::where('counselor_id', $data['counselor_id'])
                ->where('scheduled_at', $data['scheduled_at'])
                ->whereIn('status', ['requested', 'confirmed'])
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                return null;
            }

            $query = SupportQuery::create([
                'user_id' => Auth::id(),
                'category' => $data['category'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'is_anonymous' => $data['is_anonymous'] ?? false,
            ]);

            return CounselingBooking::create([
                'support_query_id' => $query->id,
                'member_id' => Auth::id(),
                'counselor_id' => $data['counselor_id'],
                'scheduled_at' => $data['scheduled_at'],
                'contact_method' => $data['contact_method'],
                'status' => 'requested',
            ]);
        });

        if (!$booking) {
            return redirect()->route('counseling.book.step', 'slot')
                ->withErrors(['slot' => 'That slot was just taken — please pick another.']);
        }

        $booking->counselor?->notify(new CounselingBookingRequested($booking));

        $this->clearWizardSession();
        session()->flash('conversion_event', 'counseling_booking_created');

        return redirect()->route('counseling.bookings.show', $booking)
            ->with('status', 'Your session request has been sent! The counselor will confirm shortly, in sha Allah.');
    }

    public function index()
    {
        $bookings = CounselingBooking::where('member_id', Auth::id())
            ->with('counselor')
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('counseling.bookings.index', compact('bookings'));
    }

    public function show(CounselingBooking $booking)
    {
        abort_unless($booking->member_id === Auth::id(), 403);
        $booking->load(['counselor', 'supportQuery.responses.responder']);

        return view('counseling.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->member_id === Auth::id(), 403);
        abort_if(in_array($booking->status, ['completed', 'cancelled']), 403);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'Your session has been cancelled.');
    }

    private function availableSlots(array $counselorIds, Carbon $date): array
    {
        $slots = [];

        foreach ($counselorIds as $counselorId) {
            foreach (CounselorAvailability::generateSlotsFor($counselorId, $date) as $time) {
                $slots[] = ['counselor_id' => $counselorId, 'datetime' => $time];
            }
        }

        usort($slots, fn ($a, $b) => $a['datetime'] <=> $b['datetime']);

        return $slots;
    }
}
