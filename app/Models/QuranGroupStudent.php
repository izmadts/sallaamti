<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranGroupStudent extends Model
{
    protected $fillable = [
        'quran_class_group_id',
        'quran_admission_id',
        'user_id',
        'joined_date',
        'completed_date',
        'status',
        'last_class_reminder_date',
        'last_fee_reminder_month',
    ];

    protected function casts(): array
    {
        return [
            'quran_class_group_id' => 'integer',
            'quran_admission_id' => 'integer',
            'user_id' => 'integer',
            'joined_date' => 'date',
            'completed_date' => 'date',
            'last_class_reminder_date' => 'date',
        ];
    }

    public function group()
    {
        return $this->belongsTo(QuranClassGroup::class, 'quran_class_group_id');
    }
    public function admission()
    {
        return $this->belongsTo(QuranAdmission::class, 'quran_admission_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
