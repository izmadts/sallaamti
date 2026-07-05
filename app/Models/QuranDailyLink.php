<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranDailyLink extends Model
{
    protected $fillable = [
        'quran_live_course_id',
        'quran_class_group_id',
        'class_date',
        'join_url',
        'passcode',
        'posted_by'
    ];

    protected function casts(): array
    {
        return ['class_date' => 'date'];
    }

    public function course()
    {
        return $this->belongsTo(QuranLiveCourse::class, 'quran_live_course_id');
    }
}
