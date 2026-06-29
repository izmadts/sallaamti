<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
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
        $isEnrolled = $course->isEnrolledBy(Auth::user());
        $progress = $isEnrolled ? $course->progressFor(Auth::user()) : 0;

        return view('courses.show', compact('course', 'isEnrolled', 'progress'));
    }

    public function enroll(Course $course)
    {
        Enrollment::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);

        return back()->with('status', 'Enrolled successfully! Start learning below.');
    }
    public function myLearning()
    {
        $enrollments = Auth::user()->enrollments()->with('course')->latest()->get();

        $courses = $enrollments->map(function ($enrollment) {
            return [
                'course' => $enrollment->course,
                'progress' => $enrollment->course->progressFor(Auth::user()),
                'enrolled_at' => $enrollment->created_at,
            ];
        });

        return view('courses.my-learning', compact('courses'));
    }
}
