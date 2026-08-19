<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranMessage extends Model
{
    protected $fillable = [
        'quran_admission_id',
        'sender_id',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'quran_admission_id' => 'integer',
            'sender_id' => 'integer',
        ];
    }

    public function admission()
    {
        return $this->belongsTo(QuranAdmission::class, 'quran_admission_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
