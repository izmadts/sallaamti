<?php

namespace App\Http\Controllers;

use App\Models\QuranAdmission;
use App\Models\QuranAssessment;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\QuranProgressReport;
use App\Models\QuranSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranLiveCourseController extends Controller
{
    public function index()
    {
        $courses = QuranLiveCourse::where('is_published', true)->with('teacher')->paginate(9);
        return view('quran-live.index', compact('courses'));
    }

    public function show(QuranLiveCourse $course)
    {
        abort_unless($course->is_published, 404);
        $admission = $course->admissionFor(Auth::user());
        $subscription = $course->subscriptionFor(Auth::user());
        $todaysLink = ($subscription && $subscription->payment_status === 'confirmed') ? $course->todaysLink() : null;

        return view('quran-live.show', compact('course', 'admission', 'subscription', 'todaysLink'));
    }

    public function admissionForm(QuranLiveCourse $course)
    {
        if ($course->admissionFor(Auth::user())) {
            return redirect()->route('quran-live.subscribe', $course);
        }
        return view('quran-live.admission', compact('course'));
    }

    public function storeAdmission(Request $request, QuranLiveCourse $course)
    {
        $validated = $request->validate([
            'guardian_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'city_state' => ['nullable', 'string', 'max:100'],
            'student_name' => ['required', 'string', 'max:255'],
            'student_gender' => ['required', 'in:male,female'],
            'student_age' => ['required', 'integer', 'min:3', 'max:80'],
            'education_grade' => ['nullable', 'string', 'max:100'],
            'learned_quran_before' => ['nullable', 'boolean'],
            'preferred_days' => ['nullable', 'array'],
            'preferred_time' => ['nullable', 'string', 'max:50'],
            'teacher_preference' => ['required', 'in:male,female,no_preference'],
            'comments' => ['nullable', 'string', 'max:1000'],
            'declaration_accepted' => ['required', 'accepted'],
            'selected_level' => ['nullable', 'string', 'max:255'],
            'previous_level' => ['nullable', 'string', 'max:255'],
            'class_type' => ['nullable', 'in:one_to_one,group'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['learned_quran_before'] = $request->has('learned_quran_before');
        $validated['declaration_accepted'] = true;
        $validated['quran_live_course_id'] = $course->id;
        $validated['user_id'] = Auth::id();

        QuranAdmission::create($validated);

        return redirect()->route('quran-live.subscribe', $course)->with('status', 'Admission submitted! Please complete this month\'s fee to receive your daily class link.');
    }

    public function subscribe(QuranLiveCourse $course)
    {
        $admission = $course->admissionFor(Auth::user());
        if (!$admission) {
            return redirect()->route('quran-live.admission', $course);
        }

        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor(Auth::user(), $month);

        return view('quran-live.subscribe', compact('course', 'subscription', 'month'));
    }

    public function storeSubscription(Request $request, QuranLiveCourse $course)
    {
        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor(Auth::user(), $month);

        if ($subscription && $subscription->payment_status === 'confirmed') {
            return redirect()->route('quran-live.show', $course)->with('status', 'Already confirmed for this month.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,easypaisa,bank_transfer'],
            'payment_reference' => ['required', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $validated['payment_screenshot'] = $request->file('payment_screenshot')->store('quran-live/payments', 'private');
        $validated['payment_status'] = 'submitted';
        $validated['payment_rejection_reason'] = null;
        $validated['amount'] = $course->monthly_fee;

        QuranSubscription::updateOrCreate(
            ['quran_live_course_id' => $course->id, 'user_id' => Auth::id(), 'month' => $month],
            $validated
        );

        return redirect()->route('quran-live.show', $course)->with('status', 'Payment submitted! Awaiting confirmation.');
    }

    public function myClass()
    {
        $user = Auth::user();

        $groupStudent = QuranGroupStudent::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['group.course', 'group.teacher'])
            ->first();

        if (!$groupStudent) {
            $admissions = QuranAdmission::where('user_id', $user->id)->with('course')->get();
            return view('quran-live.my-class', compact('admissions', 'groupStudent'));
        }

        $group = $groupStudent->group;
        $todaysLink = $group->todaysLink();
        $subscription = $group->course->subscriptionFor($user);
        $hasActiveSubscription = $subscription && $subscription->payment_status === 'confirmed';

        return view('quran-live.my-class', compact('groupStudent', 'group', 'todaysLink', 'hasActiveSubscription', 'subscription'));
    }

    public function myProgress()
    {
        $user = Auth::user();

        $groupStudent = QuranGroupStudent::where('user_id', $user->id)
            ->with('group.course')
            ->first();

        abort_unless($groupStudent, 404);

        $assessments = QuranAssessment::where('user_id', $user->id)
            ->where('quran_class_group_id', $groupStudent->quran_class_group_id)
            ->orderByDesc('assessment_date')
            ->get();

        $progressReports = QuranProgressReport::where('user_id', $user->id)
            ->where('quran_class_group_id', $groupStudent->quran_class_group_id)
            ->orderByDesc('month')
            ->get();

        return view('quran-live.my-progress', compact('groupStudent', 'assessments', 'progressReports'));
    }
}
