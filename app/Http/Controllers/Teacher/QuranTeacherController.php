<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\QuranAssessment;
use App\Models\QuranClassGroup;
use App\Models\QuranDailyLink;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\QuranProgressReport;
use App\Notifications\QuranProgressReportReady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranTeacherController extends Controller
{
    public function index()
    {
        $groups = QuranClassGroup::where('teacher_id', Auth::id())
            ->with(['course', 'activeStudents.user'])
            ->get();
        $courses = QuranLiveCourse::where('teacher_id', Auth::id())->get();
        return view('teacher.courses.index', compact('groups', 'courses'));
    }

    public function groups()
    {
        $groups = QuranClassGroup::where('teacher_id', Auth::id())
            ->with(['course', 'activeStudents.user'])
            ->get();
        return view('teacher.groups.index', compact('groups'));
    }

    public function showGroup(QuranClassGroup $group)
    {
        abort_unless($group->teacher_id === Auth::id(), 403);

        $students = $group->activeStudents()->get();
        $todaysLink = $group->todaysLink();
        $currentMonth = now()->format('Y-m');

        $assessments = QuranAssessment::where('quran_class_group_id', $group->id)
            ->where('assessment_date', '>=', now()->startOfMonth())
            ->get();

        return view('teacher.groups.show', compact('group', 'students', 'todaysLink', 'assessments', 'currentMonth'));
    }

    public function postDailyLink(Request $request, QuranClassGroup $group)
    {
        abort_unless($group->teacher_id === Auth::id(), 403);

        $validated = $request->validate([
            'join_url' => ['required', 'url'],
            'passcode' => ['nullable', 'string', 'max:50'],
        ]);

        QuranDailyLink::updateOrCreate(
            [
                'quran_class_group_id' => $group->id,
                'class_date' => now()->toDateString(),
            ],
            array_merge($validated, [
                'quran_live_course_id' => $group->quran_live_course_id,
                'posted_by' => Auth::id(),
            ])
        );

        return back()->with('status', "Today's link posted.");
    }

    public function storeAssessment(Request $request, QuranClassGroup $group, QuranGroupStudent $student)
    {
        abort_unless($group->teacher_id === Auth::id(), 403);

        $validated = $request->validate([
            'type' => ['required', 'in:weekly_quiz,monthly_test,quarterly_exam,annual_exam'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'assessment_date' => ['required', 'date'],
        ]);

        $validated['quran_class_group_id'] = $group->id;
        $validated['user_id'] = $student->user_id;
        $validated['recorded_by'] = Auth::id();
        $validated['grade'] = QuranAssessment::gradeFromScore($validated['score']);

        QuranAssessment::create($validated);

        return back()->with('status', 'Assessment recorded.');
    }

    public function storeProgressReport(Request $request, QuranClassGroup $group, QuranGroupStudent $student)
    {
        abort_unless($group->teacher_id === Auth::id(), 403);

        $validated = $request->validate([
            'month' => ['required', 'string'],
            'classes_attended' => ['required', 'integer', 'min:0'],
            'classes_total' => ['required', 'integer', 'min:0'],
            'quran_progress' => ['nullable', 'string', 'max:500'],
            'behavior' => ['nullable', 'string', 'max:500'],
            'homework_completion' => ['nullable', 'string', 'max:500'],
            'teacher_comments' => ['nullable', 'string', 'max:1000'],
            'overall_rating' => ['required', 'in:excellent,good,average,needs_improvement'],
        ]);

        $validated['quran_class_group_id'] = $group->id;
        $validated['user_id'] = $student->user_id;
        $validated['written_by'] = Auth::id();

        QuranProgressReport::updateOrCreate(
            [
                'quran_class_group_id' => $group->id,
                'user_id' => $student->user_id,
                'month' => $validated['month'],
            ],
            $validated
        );

        try {
            $student->user->notify(new \App\Notifications\QuranProgressReportReady($group, $validated['month']));
        } catch (\Throwable $e) {
            \Log::error('QuranProgressReportReady notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Progress report saved.');
    }

    // Keep backward compat
    public function show(QuranLiveCourse $course)
    {
        return redirect()->route('teacher.groups.index');
    }
    public function students()
    {
        $groupIds = QuranClassGroup::where('teacher_id', Auth::id())->pluck('id');

        $students = QuranGroupStudent::whereIn('quran_class_group_id', $groupIds)
            ->where('status', 'active')
            ->with(['user', 'group.course', 'admission'])
            ->paginate(15);

        return view('teacher.students.index', compact('students'));
    }

    public function studentDetail(QuranGroupStudent $student)
    {
        $group = $student->group;
        abort_unless($group->teacher_id === Auth::id(), 403);

        $assessments = QuranAssessment::where('user_id', $student->user_id)
            ->where('quran_class_group_id', $group->id)
            ->orderByDesc('assessment_date')
            ->get();

        $progressReports = QuranProgressReport::where('user_id', $student->user_id)
            ->where('quran_class_group_id', $group->id)
            ->orderByDesc('month')
            ->get();

        return view('teacher.students.show', compact('student', 'group', 'assessments', 'progressReports'));
    }

    public function schedule()
    {
        $groups = QuranClassGroup::where('teacher_id', Auth::id())
            ->where('is_active', true)
            ->with(['course', 'activeStudents'])
            ->get();

        return view('teacher.schedule', compact('groups'));
    }
}
