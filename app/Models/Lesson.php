<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
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
