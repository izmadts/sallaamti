<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuranLiveCourse;
use App\Models\QuranSubscription;
use App\Models\User;
use App\Rules\ApprovedTeacherRule;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranLiveCourseAdminController extends Controller
{
    public function index()
    {
        $courses = QuranLiveCourse::withCount('admissions')->with('teacher')->latest()->paginate(10);
        return view('admin.quran-live.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::role('teacher')->get();
        return view('admin.quran-live.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);
        $validated['created_by'] = Auth::id();
        $validated['is_published'] = $request->has('is_published');
        $validated['class_days'] = $request->input('class_days', []);
        $validated['topics'] = $this->parseTopics($request);

        QuranLiveCourse::create($validated);

        return redirect()->route('admin.quran-live-courses.index')->with('status', 'Course created.');
    }

    public function edit(QuranLiveCourse $quranLiveCourse)
    {
        $teachers = User::role('teacher')->get();
        return view('admin.quran-live.edit', [
            'course' => $quranLiveCourse,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, QuranLiveCourse $quranLiveCourse)
    {
        $validated = $this->validateCourse($request);
        $validated['is_published'] = $request->has('is_published');
        $validated['class_days'] = $request->input('class_days', []);
        $validated['topics'] = $this->parseTopics($request);

        $quranLiveCourse->update($validated);

        return redirect()->route('admin.quran-live-courses.index')->with('status', 'Course updated.');
    }

    // Was only ever called from update() — a newly created course's topics
    // were silently dropped regardless of what was typed into the textarea,
    // since store() never processed topics_raw at all. Shared here so both
    // save paths behave the same.
    private function parseTopics(Request $request): array
    {
        if (!$request->filled('topics_raw')) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $request->topics_raw))
        ));
    }

    public function destroy(QuranLiveCourse $quranLiveCourse)
    {
        $quranLiveCourse->delete();
        return back()->with('status', 'Course deleted.');
    }

    public function subscriptions(QuranLiveCourse $quranLiveCourse)
    {
        $subscriptions = $quranLiveCourse->subscriptions()
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('admin.quran-live.subscriptions', ['course' => $quranLiveCourse, 'subscriptions' => $subscriptions]);
    }

    public function confirmPayment(QuranSubscription $subscription)
    {
        if ($subscription->payment_status !== 'submitted') {
            return back()->with('error', "This payment is already {$subscription->payment_status} — nothing to confirm.");
        }

        $subscription->update(['payment_status' => 'confirmed', 'payment_confirmed_at' => now()]);

        try {
            $subscription->user->notify(
                new \App\Notifications\QuranLivePaymentConfirmed($subscription->course, $subscription->month)
            );
        } catch (\Throwable $e) {
            \Log::error('QuranLivePaymentConfirmed notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Payment confirmed.');
    }

    public function rejectPayment(Request $request, QuranSubscription $subscription)
    {
        $request->validate(['payment_rejection_reason' => 'required|string|max:500']);
        $subscription->update(['payment_status' => 'rejected', 'payment_rejection_reason' => $request->payment_rejection_reason]);
        return back()->with('status', 'Payment rejected.');
    }

    private function validateCourse(Request $request): array
    {
        // level_number, category, duration, gender_preference and
        // max_students_per_group all sit right there on both the create and
        // edit forms — but were missing from this whitelist entirely, the
        // same class of bug the comment below already flags for `outcome`.
        // $request->validate() silently drops anything not listed here, so
        // every one of these was thrown away on every save, no error shown,
        // ever. Confirmed against production: the one real course had all
        // five NULL despite the edit screen showing values typed into them.
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            // The form's actual "Description / Outcome" field posts as
            // `outcome` — this was missing from validation entirely, so it
            // was silently dropped on every save regardless of what an
            // admin typed in.
            'outcome' => ['nullable', 'string'],
            'level_number' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:100'],
            'teacher_id' => ['nullable', 'exists:users,id', new ApprovedTeacherRule()],
            'class_time' => ['nullable', 'string', 'max:50'],
            'min_age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'max_age' => ['nullable', 'integer', 'min:1', 'max:120', 'gte:min_age'],
            'gender_preference' => ['nullable', 'in:male,female,both'],
            'max_students_per_group' => ['nullable', 'integer', 'min:1', 'max:100'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['outcome'] = HtmlSanitizer::clean($validated['outcome'] ?? null);
        $validated['gender_preference'] = $validated['gender_preference'] ?? 'both';
        $validated['max_students_per_group'] = $validated['max_students_per_group'] ?? 10;

        return $validated;
    }
    
    
}
