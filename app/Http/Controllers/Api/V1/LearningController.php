<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\CertificatePdf;
use App\Services\QuizGrader;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

// The mobile-app side of the self-paced learning stack — CourseController,
// SkillsController, LessonController, QuizController and CertificateController
// on the web, all of which sit on the same `courses` table split by `track`
// ('quran' vs 'skills'). One controller here rather than five mirrors, because
// from the app's perspective it's a single journey: browse a track, enroll,
// work through lessons, sit the quizzes, collect the certificate.
//
// Scoring and the dwell-time gate are deliberately NOT reimplemented here —
// they live in QuizGrader and Lesson::secondsBeforeCompleteFor(), shared with
// the web controllers, so a learner is marked identically on either surface.
class LearningController extends Controller
{
    private const TRACKS = ['quran', 'skills'];

    /**
     * Completed-lesson ids for $user across $lessons, in one query instead of
     * one per lesson — a 30-lesson course otherwise costs 30 round trips just
     * to draw its contents list.
     */
    private function completedLessonIds(User $user, Collection $lessons): array
    {
        return LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();
    }

    /** Quiz ids $user has passed, out of $quizIds — same one-query reasoning. */
    private function passedQuizIds(User $user, array $quizIds): array
    {
        if (empty($quizIds)) {
            return [];
        }

        return QuizAttempt::where('user_id', $user->id)
            ->whereIn('quiz_id', $quizIds)
            ->where('passed', true)
            ->pluck('quiz_id')
            ->unique()
            ->all();
    }

    private function courseCardPayload(Course $course, bool $isEnrolled, int $completedLessons): array
    {
        $total = $course->lessons_count
            ?? ($course->relationLoaded('lessons') ? $course->lessons->count() : $course->lessons()->count());

        return [
            'id' => $course->id,
            'slug' => $course->slug,
            'title' => $course->title,
            'description' => $course->description,
            'category' => $course->category,
            'track' => $course->track,
            'level' => $course->level,
            'min_age' => $course->min_age,
            'max_age' => $course->max_age,
            'thumbnail_url' => $course->thumbnail ? Storage::url($course->thumbnail) : null,
            'lessons_count' => $total,
            'is_enrolled' => $isEnrolled,
            'completed_lessons' => $completedLessons,
            'progress' => $total > 0 ? (int) round(($completedLessons / $total) * 100) : 0,
        ];
    }

    private function quizSummaryPayload(?Quiz $quiz, bool $passed): ?array
    {
        return $quiz ? [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'passing_percentage' => $quiz->passing_percentage,
            'passed' => $passed,
        ] : null;
    }

    private function attemptPayload(?QuizAttempt $attempt): ?array
    {
        return $attempt ? [
            'id' => $attempt->id,
            'score_percentage' => $attempt->score_percentage,
            'passed' => $attempt->passed,
            'created_at' => $attempt->created_at->toIso8601String(),
        ] : null;
    }

    private function certificatePayload(Certificate $certificate): array
    {
        return [
            'id' => $certificate->id,
            'certificate_number' => $certificate->certificate_number,
            'title' => CertificatePdf::label($certificate),
            'type' => $certificate->type,
            'course_id' => $certificate->course_id,
            // 'pending'/'approved'/'rejected' — a request the student hasn't
            // heard back on yet has no certificate_number/issued_at at all.
            'status' => $certificate->status,
            'rejection_reason' => $certificate->rejection_reason,
            'issued_at' => $certificate->issued_at?->toIso8601String(),
        ];
    }

    /** Catalogue for one track, plus that track's categories for the filter chips. */
    public function courses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'track' => ['nullable', 'in:' . implode(',', self::TRACKS)],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $track = $validated['track'] ?? 'quran';
        $user = $request->user();

        $query = Course::where('is_published', true)->where('track', $track)->withCount('lessons');

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        $courses = $query->latest()->get();

        $enrolledIds = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courses->pluck('id'))
            ->pluck('course_id')
            ->all();

        $completedCounts = $this->completedCountsByCourse($user, $courses->pluck('id')->all());

        return response()->json([
            'track' => $track,
            'categories' => Course::where('is_published', true)
                ->where('track', $track)
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->values(),
            'courses' => $courses->map(fn (Course $c) => $this->courseCardPayload(
                $c,
                in_array($c->id, $enrolledIds, true),
                (int) ($completedCounts[$c->id] ?? 0)
            ))->values(),
        ]);
    }

    /**
     * Completed-lesson tallies keyed by course id — one grouped query for the
     * whole list, the same aggregate CourseController::myLearning uses on web
     * instead of calling progressFor() per course.
     */
    private function completedCountsByCourse(User $user, array $courseIds)
    {
        if (empty($courseIds)) {
            return collect();
        }

        return LessonProgress::query()
            ->join('lessons', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->where('lesson_progress.user_id', $user->id)
            ->whereNotNull('lesson_progress.completed_at')
            ->whereIn('lessons.course_id', $courseIds)
            ->selectRaw('lessons.course_id as course_id, count(*) as completed')
            ->groupBy('lessons.course_id')
            ->pluck('completed', 'course_id');
    }

    public function show(Course $course, Request $request): JsonResponse
    {
        abort_unless($course->is_published, 404);

        $user = $request->user();
        $course->load('lessons.quiz', 'quiz');

        $isEnrolled = $course->isEnrolledBy($user);
        $completedIds = $this->completedLessonIds($user, $course->lessons);
        $lessonQuizIds = $course->lessons->pluck('quiz.id')->filter()->all();
        $passedQuizIds = $this->passedQuizIds($user, $lessonQuizIds);

        // Mirrors Course::allLessonsCompletedAndPassedBy() but reuses the ids
        // already loaded above rather than re-querying per lesson.
        $allLessonsDone = $course->lessons->every(
            fn (Lesson $lesson) => in_array($lesson->id, $completedIds, true)
                && (!$lesson->quiz || in_array($lesson->quiz->id, $passedQuizIds, true))
        );

        $finalQuizPassed = $course->quiz && $course->quiz->isPassedBy($user);
        $certificate = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();

        return response()->json([
            'course' => $this->courseCardPayload($course, $isEnrolled, count($completedIds)),
            'lessons' => $course->lessons->map(fn (Lesson $lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'order' => $lesson->order,
                'has_video' => (bool) $lesson->video_url,
                'has_file' => (bool) $lesson->file_path,
                'is_completed' => in_array($lesson->id, $completedIds, true),
                'quiz' => $this->quizSummaryPayload(
                    $lesson->quiz,
                    $lesson->quiz && in_array($lesson->quiz->id, $passedQuizIds, true)
                ),
            ])->values(),
            'final_quiz' => $this->quizSummaryPayload($course->quiz, $finalQuizPassed),
            // The final quiz only opens once every lesson (and every lesson
            // quiz) is behind them — same gate QuizController::show enforces.
            'final_quiz_unlocked' => $allLessonsDone,
            'certificate_eligible' => $allLessonsDone && (!$course->quiz || $finalQuizPassed),
            'certificate' => $certificate ? $this->certificatePayload($certificate) : null,
        ]);
    }

    public function enroll(Course $course, Request $request): JsonResponse
    {
        abort_unless($course->is_published, 404);

        Enrollment::firstOrCreate([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Enrolled successfully! Start learning below.'], 201);
    }

    public function myLearning(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = $user->enrollments()
            ->with(['course' => fn ($q) => $q->withCount('lessons')])
            ->latest()
            ->get()
            // An enrollment whose course was deleted would otherwise blow up
            // on ->course->title further down.
            ->filter(fn (Enrollment $e) => $e->course !== null);

        $completedCounts = $this->completedCountsByCourse($user, $enrollments->pluck('course_id')->all());

        return response()->json([
            'courses' => $enrollments->map(fn (Enrollment $e) => [
                ...$this->courseCardPayload($e->course, true, (int) ($completedCounts[$e->course_id] ?? 0)),
                'enrolled_at' => $e->created_at->toIso8601String(),
            ])->values(),
        ]);
    }

    private function authorizeEnrolled(Course $course, User $user): void
    {
        abort_unless($course->isEnrolledBy($user), 403, 'Enroll in this course first.');
    }

    public function lesson(Lesson $lesson, Request $request): JsonResponse
    {
        $user = $request->user();
        $course = $lesson->course;
        $this->authorizeEnrolled($course, $user);

        $siblings = $course->lessons;
        $index = $siblings->search(fn (Lesson $l) => $l->id === $lesson->id);
        $isCompleted = $lesson->isCompletedBy($user);

        return response()->json([
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'content' => $lesson->content,
                'video_url' => $lesson->video_url,
                'embed_url' => $lesson->embed_url,
                'file_url' => $lesson->file_path ? Storage::url($lesson->file_path) : null,
                'file_name' => $lesson->file_name,
                'order' => $lesson->order,
                'is_completed' => $isCompleted,
                // Counts down from Lesson::MIN_SECONDS_BEFORE_COMPLETE; the
                // app disables its "Mark Complete" button until this hits 0.
                'seconds_remaining' => $lesson->secondsBeforeCompleteFor($user),
                'quiz' => $this->quizSummaryPayload($lesson->quiz, $lesson->quiz && $lesson->quiz->isPassedBy($user)),
            ],
            'course' => ['id' => $course->id, 'title' => $course->title, 'track' => $course->track],
            'previous_lesson_id' => $index > 0 ? $siblings[$index - 1]->id : null,
            'next_lesson_id' => $index !== false && $index < $siblings->count() - 1 ? $siblings[$index + 1]->id : null,
        ]);
    }

    public function completeLesson(Lesson $lesson, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeEnrolled($lesson->course, $user);

        $progress = $lesson->startProgressFor($user);
        $remaining = $lesson->secondsBeforeCompleteFor($user);

        abort_if(
            $remaining > 0,
            422,
            'Please spend a bit more time reviewing the lesson before marking it complete.'
        );

        $progress->update(['completed_at' => now()]);

        return response()->json(['status' => 'ok', 'message' => 'Lesson marked complete!']);
    }

    /** Questions without correct_option — the answer key never leaves the server. */
    private function quizDetailPayload(Quiz $quiz, User $user): array
    {
        $quiz->load('questions');

        return [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'passing_percentage' => $quiz->passing_percentage,
                'questions' => $quiz->questions->map(fn ($question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'options' => $question->options,
                    'order' => $question->order,
                ])->values(),
            ],
            'best_attempt' => $this->attemptPayload($quiz->bestAttemptFor($user)),
        ];
    }

    public function lessonQuiz(Lesson $lesson, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeEnrolled($lesson->course, $user);

        $quiz = $lesson->quiz;
        abort_unless($quiz, 404, 'No quiz available for this lesson.');

        return response()->json($this->quizDetailPayload($quiz, $user));
    }

    public function submitLessonQuiz(Lesson $lesson, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeEnrolled($lesson->course, $user);

        $quiz = $lesson->quiz;
        abort_unless($quiz, 404);

        QuizGrader::guardAgainstBruteForce($quiz, $user);
        $attempt = QuizGrader::grade($quiz, $this->submittedAnswers($request), $user);

        // Same rule as QuizController::submitLessonQuiz — passing the lesson's
        // quiz marks the lesson itself complete.
        if ($attempt->passed) {
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['completed_at' => now()]
            );
        }

        return response()->json([
            'attempt' => $this->attemptPayload($attempt),
            'message' => QuizGrader::resultMessage($attempt, $quiz),
        ], 201);
    }

    public function courseQuiz(Course $course, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeEnrolled($course, $user);

        $quiz = $course->quiz;
        abort_unless($quiz, 404, 'No quiz available for this course.');
        abort_unless(
            $course->allLessonsCompletedAndPassedBy($user),
            403,
            'Complete all lessons and pass their quizzes first.'
        );

        return response()->json($this->quizDetailPayload($quiz, $user));
    }

    public function submitCourseQuiz(Course $course, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeEnrolled($course, $user);

        $quiz = $course->quiz;
        abort_unless($quiz, 404);

        QuizGrader::guardAgainstBruteForce($quiz, $user);
        $attempt = QuizGrader::grade($quiz, $this->submittedAnswers($request), $user);

        return response()->json([
            'attempt' => $this->attemptPayload($attempt),
            'message' => QuizGrader::resultMessage($attempt, $quiz),
        ], 201);
    }

    /**
     * answers arrives as {questionId: chosenOptionIndex}. JSON object keys are
     * always strings, so they're cast back to int ids here to line up with the
     * question ids QuizGrader looks up.
     */
    private function submittedAnswers(Request $request): array
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        $answers = [];
        foreach ($request->input('answers', []) as $questionId => $option) {
            $answers[(int) $questionId] = $option === null ? null : (int) $option;
        }

        return $answers;
    }

    /**
     * Submits a certificate request rather than issuing one outright — an
     * admin has to approve it before certificate_number/issued_at exist
     * (see the certificates table migration for why). (user_id, course_id)
     * is unique, so a previously-rejected request is reset back to pending
     * rather than silently left rejected forever the way firstOrCreate
     * would leave it — resubmitting has to actually do something.
     */
    public function generateCertificate(Course $course, Request $request): JsonResponse
    {
        $user = $request->user();

        $this->authorizeEnrolled($course, $user);
        abort_unless(
            $course->isCertificateEligibleFor($user),
            403,
            'Complete the course and pass the final quiz first.'
        );

        $certificate = Certificate::firstOrNew(['user_id' => $user->id, 'course_id' => $course->id]);

        if ($certificate->isApproved()) {
            return response()->json(['certificate' => $this->certificatePayload($certificate)], 200);
        }
        abort_if($certificate->isPending() && $certificate->exists, 422, 'Your request is already awaiting review.');

        $certificate->fill(['type' => 'course', 'status' => 'pending', 'rejection_reason' => null])->save();

        return response()->json(['certificate' => $this->certificatePayload($certificate)], 201);
    }

    /** Every certificate the member holds — course ones plus any issued ID cards. */
    public function certificates(Request $request): JsonResponse
    {
        $certificates = $request->user()->certificates()->with('course')->latest()->get();

        return response()->json([
            'certificates' => $certificates->map(fn (Certificate $c) => $this->certificatePayload($c))->values(),
        ]);
    }

    public function downloadCertificate(Certificate $certificate, Request $request): Response
    {
        abort_unless((int) $certificate->user_id === (int) $request->user()->id, 403);

        return CertificatePdf::download($certificate);
    }
}
