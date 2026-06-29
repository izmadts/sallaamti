<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuranLiveCourse;
use App\Models\QuranSubscription;
use App\Models\User;
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

        QuranLiveCourse::create($validated);

        return redirect()->route('admin.quran-live-courses.index')->with('status', 'Course created.');
    }

    public function edit(QuranLiveCourse $quranLiveCourse)
    {
        $teachers = User::role('teacher')->get();
        return view('admin.quran-live.edit', ['course' => $quranLiveCourse, 'teachers' => $teachers]);
    }

    public function update(Request $request, QuranLiveCourse $quranLiveCourse)
    {
        $validated = $this->validateCourse($request);
        $validated['is_published'] = $request->has('is_published');
        $validated['class_days'] = $request->input('class_days', []);

        $quranLiveCourse->update($validated);

        return redirect()->route('admin.quran-live-courses.index')->with('status', 'Course updated.');
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
        $subscription->update(['payment_status' => 'confirmed', 'payment_confirmed_at' => now()]);
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
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'class_time' => ['nullable', 'string', 'max:50'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
