<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuranAdmission;
use App\Models\QuranAssessment;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\QuranMessage;
use App\Models\QuranProgressReport;
use App\Models\QuranSubscription;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// The mobile-app side of QuranLiveCourseController (web) — same course
// catalog, same QuranAdmission/QuranSubscription/QuranClassGroup models,
// same "one Zoom/Meet link posted per group per day" pattern. Where the web
// spreads admission across a 3-step session wizard (HasWizardSteps), mobile
// collapses it into one request, same tradeoff CounselingController already
// made — the app holds the in-progress form client-side across its own
// screens instead of round-tripping each step to the server.
class QuranLiveController extends Controller
{
    private function teacherPayload(?\App\Models\User $teacher): ?array
    {
        return $teacher ? ['id' => $teacher->id, 'name' => $teacher->name, 'avatar' => $teacher->apiAvatarUrl()] : null;
    }

    private function coursePayload(QuranLiveCourse $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'category' => $course->category,
            'level_number' => $course->level_number,
            'min_age' => $course->min_age,
            'max_age' => $course->max_age,
            'duration' => $course->duration,
            'topics' => $course->topics,
            'outcome' => $course->outcome,
            // The app shows these on the admission form up front (see
            // admissionRules' ageRule/genderRule) so a family sees "ages 5-8,
            // girls only" before filling the form, not after a rejected submit.
            'gender_preference' => $course->gender_preference,
            'class_days' => $course->class_days,
            'class_time' => $course->class_time,
            'monthly_fee' => $course->monthly_fee,
            'teacher' => $this->teacherPayload($course->teacher),
        ];
    }

    private function subscriptionPayload(?QuranSubscription $subscription): ?array
    {
        if (!$subscription) {
            return null;
        }

        return [
            'id' => $subscription->id,
            'month' => $subscription->month,
            'amount' => $subscription->amount,
            'payment_status' => $subscription->payment_status,
            'payment_method' => $subscription->payment_method,
            'payment_reference' => $subscription->payment_reference,
            'payment_rejection_reason' => $subscription->payment_rejection_reason,
        ];
    }

    private function admissionPayload(QuranAdmission $admission): array
    {
        $course = $admission->course;
        $subscription = $course->subscriptionFor($admission);
        $confirmed = $subscription && $subscription->payment_status === 'confirmed';

        return [
            'id' => $admission->id,
            'course_id' => $admission->quran_live_course_id,
            'student_name' => $admission->student_name,
            'status' => $admission->status,
            // Only meaningful for 'rejected' today, but harmless to always
            // include — the app couldn't previously explain a rejection at
            // all, since this never left the server.
            'admin_notes' => $admission->admin_notes,
            'subscription' => $this->subscriptionPayload($subscription),
            'todays_link' => $confirmed ? $this->linkPayload($course->todaysLink()) : null,
        ];
    }

    private function linkPayload(?\App\Models\QuranDailyLink $link): ?array
    {
        return $link ? ['join_url' => $link->join_url, 'passcode' => $link->passcode] : null;
    }

    public function meta(): JsonResponse
    {
        $levels = QuranLiveCourse::where('is_published', true)->orderBy('level_number')->get(['id', 'title', 'level_number']);

        return response()->json([
            'grades' => [
                'Pre-school / Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5',
                'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'O-Levels', 'A-Levels',
                'Grade 11', 'Grade 12', 'Undergraduate', 'Graduate / Postgraduate',
                'Homeschooled', 'Not currently in school', 'Other',
            ],
            'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'times' => [
                '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM',
                '7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM', '12:00 AM',
            ],
            'teacher_preferences' => ['no_preference' => 'No Preference', 'male' => 'Male Teacher', 'female' => 'Female Teacher'],
            'class_types' => ['group' => 'Group Class', 'one_to_one' => 'One-to-One (Private)'],
            'levels' => $levels->map(fn (QuranLiveCourse $c) => ['title' => $c->title, 'level_number' => $c->level_number]),
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function courses(): JsonResponse
    {
        $courses = QuranLiveCourse::where('is_published', true)->with('teacher')->orderBy('level_number')->get();

        return response()->json(['courses' => $courses->map(fn (QuranLiveCourse $c) => $this->coursePayload($c))->values()]);
    }

    public function show(QuranLiveCourse $course, Request $request): JsonResponse
    {
        abort_unless($course->is_published, 404);

        $admissions = $course->admissionsFor($request->user());

        return response()->json([
            'course' => $this->coursePayload($course),
            'admissions' => $admissions->map(fn (QuranAdmission $a) => $this->admissionPayload($a))->values(),
        ]);
    }

    // Mirrors QuranLiveCourseController's web-side rule — kept in sync
    // deliberately rather than shared, since the two controllers don't
    // otherwise share a base class; a course's age/gender restriction must
    // reject the same way whichever surface a family applies from.
    private function ageRule(QuranLiveCourse $course): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($course) {
            if ($course->min_age !== null && $value < $course->min_age) {
                $fail("This course is for ages {$course->min_age}" . ($course->max_age ? "–{$course->max_age}" : '+') . '.');
            } elseif ($course->max_age !== null && $value > $course->max_age) {
                $fail("This course is for ages " . ($course->min_age ?? 1) . "–{$course->max_age}.");
            }
        };
    }

    private function genderRule(QuranLiveCourse $course): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($course) {
            if ($course->gender_preference && $course->gender_preference !== 'both' && $value !== $course->gender_preference) {
                $fail("This course is for {$course->gender_preference} students only.");
            }
        };
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
            'student_gender' => ['required', 'in:male,female', $this->genderRule($course)],
            'student_age' => ['required', 'integer', 'min:3', 'max:80', $this->ageRule($course)],
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
            'declaration_accepted' => ['required', 'accepted'],
        ];
    }

    public function storeAdmission(Request $request, QuranLiveCourse $course): JsonResponse
    {
        abort_unless($course->is_published, 404);

        $validated = $request->validate($this->admissionRules($course), [
            'student_name.unique' => 'You already have an admission under this name for this course. Use a different name if this is a different child.',
        ]);

        $validated['learned_quran_before'] = $request->boolean('learned_quran_before');
        $validated['declaration_accepted'] = true;
        $validated['quran_live_course_id'] = $course->id;
        $validated['user_id'] = $request->user()->id;

        $admission = QuranAdmission::create($validated);

        return response()->json(['admission' => $this->admissionPayload($admission->fresh())], 201);
    }

    private function authorizeAdmissionOwner(QuranAdmission $admission, Request $request): void
    {
        abort_unless((int) $admission->user_id === (int) $request->user()->id, 403);
    }

    public function subscription(QuranAdmission $admission, Request $request): JsonResponse
    {
        $this->authorizeAdmissionOwner($admission, $request);

        $course = $admission->course;
        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor($admission, $month);

        return response()->json([
            'month' => $month,
            'amount' => $course->monthly_fee,
            'subscription' => $this->subscriptionPayload($subscription),
            'payment_instructions' => [
                'jazzcash_number' => setting('jazzcash_number'),
                'jazzcash_account_title' => setting('jazzcash_account_title'),
                'bank_name' => setting('bank_name'),
                'bank_account_title' => setting('bank_account_title'),
                'bank_account_number' => setting('bank_account_number'),
                'bank_account_iban' => setting('bank_account_iban'),
            ],
        ]);
    }

    public function storeSubscription(Request $request, QuranAdmission $admission): JsonResponse
    {
        $this->authorizeAdmissionOwner($admission, $request);

        $course = $admission->course;
        $month = now()->format('Y-m');
        $subscription = $course->subscriptionFor($admission, $month);

        abort_if($subscription && $subscription->payment_status === 'confirmed', 422, 'Already confirmed for this month.');

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
        $validated['user_id'] = $request->user()->id;

        $subscription = QuranSubscription::updateOrCreate(
            ['quran_admission_id' => $admission->id, 'month' => $month],
            $validated
        );

        return response()->json(['subscription' => $this->subscriptionPayload($subscription)], 201);
    }

    private function groupStudentPayload(QuranGroupStudent $gs): array
    {
        $group = $gs->group;
        $course = $group->course;
        $subscription = $course->subscriptionFor($gs->admission);
        $confirmed = $subscription && $subscription->payment_status === 'confirmed';

        return [
            'id' => $gs->id,
            'admission_id' => $gs->quran_admission_id,
            'student_name' => $gs->admission?->student_name ?? $gs->user?->name,
            'course_title' => $course->title,
            'group_name' => $group->group_name,
            'teacher' => $this->teacherPayload($group->teacher),
            'class_days' => $group->class_days,
            'class_time' => $group->class_time,
            'timezone' => $group->timezone,
            'subscription' => $this->subscriptionPayload($subscription),
            'todays_link' => $confirmed ? $this->linkPayload($group->todaysLink()) : null,
        ];
    }

    private function messagePayload(QuranMessage $message, int $viewerId): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'is_mine' => (int) $message->sender_id === $viewerId,
            'sender_name' => $message->sender?->name,
            'created_at' => $message->created_at->toIso8601String(),
        ];
    }

    public function myClass(Request $request): JsonResponse
    {
        $user = $request->user();

        $admissions = QuranAdmission::where('user_id', $user->id)->with('course')->get();

        $groupStudents = QuranGroupStudent::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['group.course', 'group.teacher', 'admission.messages.sender'])
            ->get();

        if ($groupStudents->isEmpty()) {
            return response()->json([
                'admissions' => $admissions->map(fn (QuranAdmission $a) => [
                    'id' => $a->id,
                    'student_name' => $a->student_name,
                    'course_title' => $a->course->title,
                    'status' => $a->status,
                    'admin_notes' => $a->admin_notes,
                    'preferred_time' => $a->preferred_time,
                    'teacher_preference' => $a->teacher_preference,
                ])->values(),
                'group_students' => [],
            ]);
        }

        $requestedChildId = (int) $request->query('child', 0);
        $current = $groupStudents->firstWhere('id', $requestedChildId) ?? $groupStudents->first();

        return response()->json([
            'admissions' => $admissions->map(fn (QuranAdmission $a) => [
                'id' => $a->id,
                'student_name' => $a->student_name,
                'course_title' => $a->course->title,
                'status' => $a->status,
            ])->values(),
            'group_students' => $groupStudents->map(fn (QuranGroupStudent $gs) => $this->groupStudentPayload($gs))->values(),
            'current_id' => $current->id,
            'messages' => $current->admission
                ? $current->admission->messages->map(fn (QuranMessage $m) => $this->messagePayload($m, $user->id))->values()
                : [],
        ]);
    }

    public function sendMessage(Request $request, QuranAdmission $admission): JsonResponse
    {
        $this->authorizeAdmissionOwner($admission, $request);

        $validated = $request->validate(['message' => ['required', 'string', 'min:1', 'max:2000']]);

        $message = QuranMessage::create([
            'quran_admission_id' => $admission->id,
            'sender_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);
        $message->load('sender');

        return response()->json(['message' => $this->messagePayload($message, $request->user()->id)], 201);
    }

    public function myProgress(Request $request): JsonResponse
    {
        $user = $request->user();

        $groupStudents = QuranGroupStudent::where('user_id', $user->id)->with(['group.course', 'admission'])->get();
        abort_unless($groupStudents->isNotEmpty(), 404);

        $requestedChildId = (int) $request->query('child', 0);
        $current = $groupStudents->firstWhere('id', $requestedChildId) ?? $groupStudents->first();

        $assessments = QuranAssessment::where('quran_admission_id', $current->quran_admission_id)
            ->where('quran_class_group_id', $current->quran_class_group_id)
            ->orderByDesc('assessment_date')
            ->get();

        $progressReports = QuranProgressReport::where('quran_admission_id', $current->quran_admission_id)
            ->where('quran_class_group_id', $current->quran_class_group_id)
            ->orderByDesc('month')
            ->get();

        return response()->json([
            'group_students' => $groupStudents->map(fn (QuranGroupStudent $gs) => [
                'id' => $gs->id,
                'student_name' => $gs->admission?->student_name ?? $gs->user?->name,
            ])->values(),
            'current_id' => $current->id,
            'assessments' => $assessments->map(fn (QuranAssessment $a) => [
                'id' => $a->id,
                'type' => $a->type,
                'score' => $a->score,
                'grade' => $a->grade,
                'remarks' => $a->remarks,
                'assessment_date' => $a->assessment_date->toDateString(),
            ])->values(),
            'progress_reports' => $progressReports->map(fn (QuranProgressReport $r) => [
                'id' => $r->id,
                'month' => $r->month,
                'classes_attended' => $r->classes_attended,
                'classes_total' => $r->classes_total,
                'quran_progress' => $r->quran_progress,
                'behavior' => $r->behavior,
                'homework_completion' => $r->homework_completion,
                'teacher_comments' => $r->teacher_comments,
                'overall_rating' => $r->overall_rating,
            ])->values(),
        ]);
    }
}
