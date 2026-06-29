<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question', 'options', 'correct_option', 'order'];

    protected function casts(): array
    {
        return ['options' => 'array'];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
