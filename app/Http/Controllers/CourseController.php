<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('is_published', true)->withCount('lessons');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $courses = $query->latest()->paginate(9)->withQueryString();
        $categories = Course::where('is_published', true)->whereNotNull('category')->distinct()->pluck('category');

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        abort_unless($course->is_published, 404);
        $course->load('lessons');

        // Guest-safe checks
        $isEnrolled = auth()->check() && $course->isEnrolledBy(Auth::user());
        $progress = $isEnrolled ? $course->progressFor(Auth::user()) : 0;

        return view('courses.show', compact('course', 'isEnrolled', 'progress'));
    }

    public function enroll(Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('status', 'Please log in or register to enroll in this course.');
        }

        Enrollment::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);

        session()->flash('conversion_event', 'course_enrolled');
        session()->flash('conversion_event_data', ['course' => $course->title]);

        return back()->with('status', 'Enrolled successfully! Start learning below.');
    }
    public function myLearning()
    {
        $enrollments = Auth::user()->enrollments()
            ->with(['course' => fn ($q) => $q->withCount('lessons')])
            ->latest()
            ->get();

        $courseIds = $enrollments->pluck('course_id');

        // One aggregated query for completed-lesson counts across every
        // enrolled course, instead of 3 queries per course via progressFor().
        $completedCounts = LessonProgress::query()
            ->join('lessons', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->where('lesson_progress.user_id', Auth::id())
            ->whereNotNull('lesson_progress.completed_at')
            ->whereIn('lessons.course_id', $courseIds)
            ->selectRaw('lessons.course_id as course_id, count(*) as completed')
            ->groupBy('lessons.course_id')
            ->pluck('completed', 'course_id');

        $courses = $enrollments->map(function ($enrollment) use ($completedCounts) {
            $total = $enrollment->course->lessons_count;
            $completed = $completedCounts[$enrollment->course_id] ?? 0;

            return [
                'course' => $enrollment->course,
                'progress' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                'enrolled_at' => $enrollment->created_at,
            ];
        });

        return view('courses.my-learning', compact('courses'));
    }
}
