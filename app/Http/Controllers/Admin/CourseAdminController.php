<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseAdminController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons', 'enrollments')->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = Auth::id();
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validateCourse($request);
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('status', 'Course deleted.');
    }

    public function show(Course $course)
    {
        $course->load('lessons');
        return view('admin.courses.show', compact('course'));
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:50'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    // Lessons
    public function createLesson(Course $course)
    {
        return view('admin.lessons.create', compact('course'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'order' => ['nullable', 'integer'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['order'] = $validated['order'] ?? ($course->lessons()->max('order') + 1);

        Lesson::create($validated);

        return redirect()->route('admin.courses.show', $course)->with('status', 'Lesson added.');
    }

    public function editLesson(Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'order' => ['nullable', 'integer'],
        ]);

        $lesson->update($validated);

        return redirect()->route('admin.courses.show', $lesson->course)->with('status', 'Lesson updated.');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $course = $lesson->course;
        $lesson->delete();
        return redirect()->route('admin.courses.show', $course)->with('status', 'Lesson deleted.');
    }
}
