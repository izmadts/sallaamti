<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

// Just the branded landing page — everything past this (course show, enroll,
// lessons, quizzes, certificates) reuses CourseController/LessonController/
// QuizController/CertificateController unchanged, since none of those views
// carry Quran-specific branding. Same underlying `courses` table, filtered
// by track instead of a parallel subsystem.
class SkillsController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('is_published', true)->where('track', 'skills')->withCount('lessons');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $courses = $query->latest()->paginate(9)->withQueryString();
        $categories = Course::where('is_published', true)->where('track', 'skills')->whereNotNull('category')->distinct()->pluck('category');

        return view('skills.index', compact('courses', 'categories'));
    }
}
