<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuranAdmission;
use App\Models\QuranClassGroup;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\User;
use App\Rules\ApprovedTeacherRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuranClassGroupAdminController extends Controller
{
    public function index(QuranLiveCourse $course)
    {
        $groups = $course->classGroups()->with('teacher', 'students.admission')->get();
        return view('admin.quran-live.groups.index', compact('course', 'groups'));
    }

    public function create(QuranLiveCourse $course)
    {
        $teachers = User::role('teacher')->get();
        return view('admin.quran-live.groups.create', compact('course', 'teachers'));
    }

    public function store(Request $request, QuranLiveCourse $course)
    {
        $validated = $request->validate([
            'group_name' => ['required', 'string', 'max:255'],
            'teacher_id' => ['nullable', 'exists:users,id', new ApprovedTeacherRule()],
            'class_days' => ['required', 'array'],
            'class_time' => ['required', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,mixed'],
            'max_students' => ['required', 'integer', 'min:1'],
        ]);

        $validated['quran_live_course_id'] = $course->id;
        $validated['is_active'] = $request->has('is_active');

        QuranClassGroup::create($validated);

        return redirect()->route('admin.quran-live-courses.groups.index', $course)
            ->with('status', 'Group created successfully.');
    }

    public function edit(QuranClassGroup $group)
    {
        $teachers = User::role('teacher')->get();
        $course = $group->course;
        return view('admin.quran-live.groups.edit', compact('group', 'course', 'teachers'));
    }

    public function update(Request $request, QuranClassGroup $group)
    {
        $validated = $request->validate([
            'group_name' => ['required', 'string', 'max:255'],
            'teacher_id' => ['nullable', 'exists:users,id', new ApprovedTeacherRule()],
            'class_days' => ['required', 'array'],
            'class_time' => ['required', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,mixed'],
            'max_students' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $group->update($validated);

        return redirect()->route('admin.quran-live-courses.groups.index', $group->course)
            ->with('status', 'Group updated.');
    }

    public function admissions()
    {
        $admissions = QuranAdmission::with(['user', 'course', 'assignedGroup.teacher'])
            ->latest()
            ->paginate(15);

        return view('admin.quran-live.admissions', compact('admissions'));
    }

    public function assignToGroup(Request $request, QuranAdmission $admission)
    {
        // Scoped to this admission's own course — exists:quran_class_groups,id
        // alone would accept ANY group in the system, which is exactly how a
        // 6-year-old's Nazrah admission could end up assigned into an
        // unrelated Tajweed/Advanced group. The dropdown itself is now also
        // scoped (admissions.blade.php), so this is the server-side backstop
        // for that, not the only guard.
        $request->validate([
            'group_id' => [
                'required',
                Rule::exists('quran_class_groups', 'id')->where('quran_live_course_id', $admission->quran_live_course_id),
            ],
        ]);

        $group = QuranClassGroup::findOrFail($request->group_id);

        if ($group->gender !== 'mixed' && $group->gender !== $admission->student_gender) {
            return back()->with('error', "{$group->group_name} is a {$group->gender}-only group — {$admission->student_name} is {$admission->student_gender}.");
        }

        if ($group->isFull()) {
            return back()->with('error', "{$group->group_name} is full.");
        }

        // Moving a student who's already actively placed elsewhere: without
        // this, the old QuranGroupStudent row is left untouched at 'active'
        // while a second one is created here, leaving them occupying a seat
        // in both groups simultaneously — assigned_group_id (a single FK)
        // would only ever reflect the newest one, silently hiding the stale
        // membership. 'dropped' is reused rather than adding a distinct
        // "transferred" status — the group enum has no such concept, and
        // "no longer active here, because moved" is what dropped means.
        QuranGroupStudent::where('quran_admission_id', $admission->id)
            ->where('quran_class_group_id', '!=', $group->id)
            ->where('status', 'active')
            ->update(['status' => 'dropped']);

        // Matched by admission (child), not just user_id — two siblings
        // assigned to the same group must land as two separate rows.
        $groupStudent = QuranGroupStudent::firstOrCreate([
            'quran_class_group_id' => $group->id,
            'quran_admission_id' => $admission->id,
        ], [
            'user_id' => $admission->user_id,
            'joined_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        // firstOrCreate leaves an existing-but-dropped row (e.g. re-assigning
        // back to a group they were previously removed from) at 'dropped' —
        // reactivate it rather than silently doing nothing.
        if ($groupStudent->status !== 'active') {
            $groupStudent->update(['status' => 'active', 'completed_date' => null]);
        }

        $admission->update([
            'assigned_group_id' => $group->id,
            'status' => 'assigned',
        ]);

        try {
            $admission->user->notify(new \App\Notifications\QuranClassAssigned($group));
        } catch (\Throwable $e) {
            \Log::error('QuranClassAssigned notification failed: ' . $e->getMessage());
        }

        return back()->with('status', "{$admission->student_name} assigned to {$group->group_name}.");
    }

    public function rejectAdmission(Request $request, QuranAdmission $admission)
    {
        $request->validate(['admin_notes' => 'required|string|max:500']);

        $admission->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('status', 'Admission rejected.');
    }

    public function updateStudentStatus(Request $request, QuranGroupStudent $student)
    {
        $request->validate(['status' => 'required|in:active,on_hold,completed,dropped']);
        $student->update(['status' => $request->status]);

        if ($request->status === 'completed') {
            $student->update(['completed_date' => now()->toDateString()]);
        }

        // This used to only ever touch QuranGroupStudent — the linked
        // QuranAdmission permanently kept reading 'assigned' even after a
        // student was dropped or completed, since nothing synced the two.
        // assigned_group_id is deliberately left as-is (historical record of
        // which group they were in), only the current-state status moves.
        if ($student->admission && in_array($request->status, ['completed', 'dropped'], true)) {
            $student->admission->update(['status' => $request->status]);
        } elseif ($student->admission && $request->status === 'active' && $student->admission->status !== 'assigned') {
            $student->admission->update(['status' => 'assigned']);
        }

        return back()->with('status', 'Student status updated.');
    }

    public function destroyGroup(QuranClassGroup $group)
    {
        $course = $group->course;
        $group->delete();

        return redirect()->route('admin.quran-live-courses.groups.index', $course)
            ->with('status', 'Group deleted.');
    }
}
