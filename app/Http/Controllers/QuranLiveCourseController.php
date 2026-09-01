<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Models\QuranAdmission;
use App\Models\QuranAssessment;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\QuranMessage;
use App\Models\QuranProgressReport;
use App\Models\QuranSubscription;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuranLiveCourseController extends Controller
{
    use HasWizardSteps;

    protected string $wizardKey = 'quran_admission';

    protected array $wizardStepFields = [
        'parent' => ['guardian_name', 'whatsapp_number', 'country', 'city_state'],
        'student' => ['student_name', 'student_gender', 'student_age', 'education_grade', 'learned_quran_before'],
        'preferences' => ['preferred_days', 'preferred_days.*', 'preferred_time', 'timezone', 'teacher_preference', 'selected_level', 'previous_level', 'class_type', 'comments'],
    ];

    protected array $admissionStepTitles = [
        'parent' => 'Parent & Contact',
        'student' => 'About the Student',
        'preferences' => 'Class Preferences',
    ];

    public function index()
    {
        $courses = QuranLiveCourse::where('is_published', true)->with('teacher')->paginate(9);
        return view('quran-live.index', compact('courses'));
    }

    public function show(QuranLiveCourse $course)
    {
        abort_unless($course->is_published, 404);

        // A family can have more than one child admitted to the same
        // course, each with their own subscription/status — the view loops
        // these instead of assuming a single admission.
        $admissions = auth()->check() ? $course->admissionsFor(Auth::user()) : collect();

        return view('quran-live.show', compact('course', 'admissions'));
    }

    // Every wizard method below scopes the session key to this specific
    // course — a parent can be mid-way through an admission for one course
    // and separately start one for another without the two colliding, and
    // (per sibling support) a second child's admission for the SAME course
    // just starts a fresh session once the first one finalizes and clears.
    private function scopeAdmissionWizardTo(QuranLiveCourse $course): void
    {
        $this->wizardKey = "quran_admission_{$course->id}";
    }

    private function admissionRules(QuranLiveCourse $course): array
    {
        return [
            'guardian_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'city_state' => ['nullable', 'string', 'max:100'],
            'student_name' => [
                'required', 'string', 'max:255',
                Rule::unique('quran_admissions')->where(
                    fn ($query) => $query->where('quran_live_course_id', $course->id)->where('user_id', Auth::id())
                ),
            ],
            'student_gender' => ['required', 'in:male,female'],
            'student_age' => ['required', 'integer', 'min:3', 'max:80'],
            'education_grade' => ['nullable', 'string', 'max:100'],
            'learned_quran_before' => ['nullable', 'boolean'],
            'preferred_days' => ['nullable', 'array'],
            'preferred_days.*' => ['string'],
            'preferred_time' => ['nullable', 'string', 'max:50'],
            'teacher_preference' => ['required', 'in:male,female,no_preference'],
            'comments' => ['nullable', 'string', 'max:1000'],
            'selected_level' => ['nullable', 'string', 'max:255'],
            'previous_level' => ['nullable', 'string', 'max:255'],
            'class_type' => ['nullable', 'in:one_to_one,group'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function admissionStart(QuranLiveCourse $course)
    {
        $this->scopeAdmissionWizardTo($course);

        $step = $this->firstIncompleteWizardStep();

        return $step
            ? redirect()->route('quran-live.admission.step', [$course, $step])
            : redirect()->route('quran-live.admission.review', $course);
    }

    public function admissionShowStep(QuranLiveCourse $course, string $step)
    {
        $this->scopeAdmissionWizardTo($course);

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        return view("quran-live.wizard.step-{$step}", [
            'course' => $course,
            'step' => $step,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->admissionStepTitles,
            'data' => $this->wizardStepData($step),
        ]);
    }

    public function admissionSaveStep(Request $request, QuranLiveCourse $course, string $step)
    {
        $this->scopeAdmissionWizardTo($course);

        abort_unless(array_key_exists($step, $this->wizardStepFields), 404);

        $rules = collect($this->admissionRules($course))->only($this->wizardStepFields[$step])->toArray();
        $validated = $request->validate($rules, [
            'student_name.unique' => 'You already have an admission under this name for this course. Use a different name if this is a different child.',
        ]);

        if ($step === 'student') {
            $validated['learned_quran_before'] = $request->boolean('learned_quran_before');
        }

        $this->saveWizardStep($step, $validated);

        $steps = $this->wizardSteps();
        $nextIndex = array_search($step, $steps) + 1;

        return $nextIndex < count($steps)
            ? redirect()->route('quran-live.admission.step', [$course, $steps[$nextIndex]])
            : redirect()->route('quran-live.admission.review', $course);
    }

    public function admissionReview(QuranLiveCourse $course)
    {
        $this->scopeAdmissionWizardTo($course);

        if ($incomplete = $this->firstIncompleteWizardStep()) {
            return redirect()->route('quran-live.admission.step', [$course, $incomplete]);
        }

        return view('quran-live.wizard.review', [
            'course' => $course,
            'steps' => $this->wizardSteps(),
            'stepTitles' => $this->admissionStepTitles,
            'data' => $this->wizardAllData(),
        ]);
    }

    public function admissionFinalize(Request $request, QuranLiveCourse $course)
    {
        $this->scopeAdmissionWizardTo($course);

        if ($this->firstIncompleteWizardStep()) {
            return redirect()->route('quran-live.admission', $course);
        }

        $request->validate(['declaration_accepted' => ['required', 'accepted']]);

        $validated = $this->wizardAllData();
        $validated['declaration_accepted'] = true;
        $validated['quran_live_course_id'] = $course->id;
        $validated['user_id'] = Auth::id();

        $admission = QuranAdmission::create($validated);

        $this->clearWizardSession();

        return redirect()->route('quran-live.subscribe', [$course, $admission])
            ->with('status', "Admission submitted for {$admission->student_name}! Please complete this month's fee to receive their daily class link.");
    }

    public function subscribe(QuranLiveCourse $course, QuranAdmission $admission)
    {
        abort_unless((int) $admission->user_id === (int) Auth::id() && (int) $admission->quran_live_course_id === (int) $course->id, 403);

        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor($admission, $month);

        return view('quran-live.subscribe', compact('course', 'admission', 'subscription', 'month'));
    }

    public function storeSubscription(Request $request, QuranLiveCourse $course, QuranAdmission $admission)
    {
        abort_unless((int) $admission->user_id === (int) Auth::id() && (int) $admission->quran_live_course_id === (int) $course->id, 403);

        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor($admission, $month);

        if ($subscription && $subscription->payment_status === 'confirmed') {
            return redirect()->route('quran-live.show', $course)->with('status', 'Already confirmed for this month.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,easypaisa,bank_transfer'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'quran-live/payments', 'private', maxDimension: 1600, quality: 82);
        $validated['payment_status'] = 'submitted';
        $validated['payment_rejection_reason'] = null;
        $validated['amount'] = $course->monthly_fee;
        $validated['quran_live_course_id'] = $course->id;
        $validated['user_id'] = Auth::id();

        QuranSubscription::updateOrCreate(
            ['quran_admission_id' => $admission->id, 'month' => $month],
            $validated
        );

        return redirect()->route('quran-live.show', $course)->with('status', "Payment submitted for {$admission->student_name}! Awaiting confirmation.");
    }

    public function myClass(Request $request)
    {
        $user = Auth::user();

        $admissions = QuranAdmission::where('user_id', $user->id)->with('course')->get();

        $groupStudents = QuranGroupStudent::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['group.course', 'group.teacher', 'admission.messages.sender'])
            ->get();

        if ($groupStudents->isEmpty()) {
            return view('quran-live.my-class', compact('admissions', 'groupStudents'));
        }

        $current = $groupStudents->firstWhere('id', (int) $request->query('child')) ?? $groupStudents->first();

        $group = $current->group;
        $todaysLink = $group->todaysLink();
        $subscription = $group->course->subscriptionFor($current->admission);
        $hasActiveSubscription = $subscription && $subscription->payment_status === 'confirmed';

        return view('quran-live.my-class', compact(
            'admissions', 'groupStudents', 'current', 'group', 'todaysLink', 'subscription', 'hasActiveSubscription'
        ));
    }

    public function reply(Request $request, QuranAdmission $admission)
    {
        abort_unless((int) $admission->user_id === (int) Auth::id(), 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        QuranMessage::create([
            'quran_admission_id' => $admission->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return back()->with('status', 'Message sent.');
    }

    public function myProgress(Request $request)
    {
        $user = Auth::user();

        $groupStudents = QuranGroupStudent::where('user_id', $user->id)
            ->with(['group.course', 'admission'])
            ->get();

        abort_unless($groupStudents->isNotEmpty(), 404);

        $current = $groupStudents->firstWhere('id', (int) $request->query('child')) ?? $groupStudents->first();

        $assessments = QuranAssessment::where('quran_admission_id', $current->quran_admission_id)
            ->where('quran_class_group_id', $current->quran_class_group_id)
            ->orderByDesc('assessment_date')
            ->get();

        $progressReports = QuranProgressReport::where('quran_admission_id', $current->quran_admission_id)
            ->where('quran_class_group_id', $current->quran_class_group_id)
            ->orderByDesc('month')
            ->get();

        return view('quran-live.my-progress', compact('groupStudents', 'current', 'assessments', 'progressReports'));
    }
}
