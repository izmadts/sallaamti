<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranProgressReport extends Model
{
    protected $fillable = [
        'quran_class_group_id',
        'user_id',
        'month',
        'classes_attended',
        'classes_total',
        'quran_progress',
        'behavior',
        'homework_completion',
        'teacher_comments',
        'overall_rating',
        'written_by',
    ];

    public function group()
    {
        return $this->belongsTo(QuranClassGroup::class, 'quran_class_group_id');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'written_by');
    }
}
