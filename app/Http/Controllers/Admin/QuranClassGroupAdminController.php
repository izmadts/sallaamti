<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuranAdmission;
use App\Models\QuranClassGroup;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranClassGroupAdminController extends Controller
{
    public function index(QuranLiveCourse $course)
    {
        $groups = $course->classGroups()->with('teacher', 'students')->get();
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
            'teacher_id' => ['nullable', 'exists:users,id'],
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
            'teacher_id' => ['nullable', 'exists:users,id'],
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
        $request->validate([
            'group_id' => ['required', 'exists:quran_class_groups,id'],
        ]);

        $group = QuranClassGroup::findOrFail($request->group_id);

        abort_if($group->isFull(), 422, 'This group is full.');

        QuranGroupStudent::firstOrCreate([
            'quran_class_group_id' => $group->id,
            'user_id' => $admission->user_id,
        ], [
            'quran_admission_id' => $admission->id,
            'joined_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $admission->update([
            'assigned_group_id' => $group->id,
            'status' => 'assigned',
        ]);

        try {
            $admission->user->notify(new \App\Notifications\QuranClassAssigned($group));
        } catch (\Throwable $e) {
            \Log::error('QuranClassAssigned notification failed: ' . $e->getMessage());
        }

        return back()->with('status', "Student assigned to {$group->group_name}.");
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
