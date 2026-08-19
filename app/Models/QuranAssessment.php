<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranAssessment extends Model
{
    protected $fillable = [
        'quran_class_group_id',
        'user_id',
        'quran_admission_id',
        'type',
        'score',
        'grade',
        'remarks',
        'assessment_date',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'quran_class_group_id' => 'integer',
            'user_id' => 'integer',
            'quran_admission_id' => 'integer',
            'assessment_date' => 'date',
        ];
    }

    public function group()
    {
        return $this->belongsTo(QuranClassGroup::class, 'quran_class_group_id');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function admission()
    {
        return $this->belongsTo(QuranAdmission::class, 'quran_admission_id');
    }
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function gradeFromScore(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A+',
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            default => 'F',
        };
    }
}
