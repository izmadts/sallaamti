<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\QuranDailyLink;
use App\Models\QuranLiveCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranTeacherController extends Controller
{
    public function index()
    {
        $courses = QuranLiveCourse::where('teacher_id', Auth::id())->get();
        return view('teacher.courses.index', compact('courses'));
    }

    public function show(QuranLiveCourse $course)
    {
        abort_unless($course->teacher_id === Auth::id(), 403);

        $confirmedStudents = $course->subscriptions()
            ->where('month', now()->format('Y-m'))
            ->where('payment_status', 'confirmed')
            ->with('user')
            ->get();

        $todaysLink = $course->todaysLink();

        return view('teacher.courses.show', compact('course', 'confirmedStudents', 'todaysLink'));
    }

    public function postDailyLink(Request $request, QuranLiveCourse $course)
    {
        abort_unless($course->teacher_id === Auth::id(), 403);

        $validated = $request->validate([
            'join_url' => ['required', 'url'],
            'passcode' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['quran_live_course_id'] = $course->id;
        $validated['class_date'] = now()->toDateString();
        $validated['posted_by'] = Auth::id();

        QuranDailyLink::updateOrCreate(
            ['quran_live_course_id' => $course->id, 'class_date' => now()->toDateString()],
            $validated
        );

        return back()->with('status', "Today's class link posted/updated.");
    }
}
