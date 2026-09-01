<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    /**
     * Minimum time a lesson must be open before it can be marked complete —
     * stops a learner clicking straight through a course to the certificate.
     */
    public const MIN_SECONDS_BEFORE_COMPLETE = 20;

    protected $fillable = ['course_id', 'title', 'content', 'video_url', 'file_path', 'file_name', 'order'];

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $this->id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * The learner's progress row, stamping started_at the first time they
     * open the lesson. firstOrCreate only writes started_at on creation, so
     * revisiting a lesson never restarts its timer.
     */
    public function startProgressFor(User $user): LessonProgress
    {
        return LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $this->id],
            ['started_at' => now()]
        );
    }

    /**
     * Seconds still to wait before this lesson may be marked complete, 0 once
     * the minimum has elapsed.
     *
     * Note the diff direction: Carbon 3 made diffInSeconds() signed, so it
     * must read started_at -> now to come out positive. The reverse
     * ($now->diffInSeconds($started)) yields a negative age, which previously
     * made the gate permanently unsatisfiable.
     */
    public function secondsBeforeCompleteFor(User $user): int
    {
        if ($this->isCompletedBy($user)) {
            return 0;
        }

        $startedAt = $this->startProgressFor($user)->started_at ?? now();

        return (int) max(0, self::MIN_SECONDS_BEFORE_COMPLETE - $startedAt->diffInSeconds(now()));
    }
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        $url = $this->video_url;

        // Already an embed URL
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        // Standard watch URL: youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Short URL: youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Fallback: return as-is (e.g. Vimeo or other already-embeddable links)
        return $url;
    }
}
