<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingQuizQuestion extends Model
{
    protected $fillable = ['question_en', 'question_ur', 'options', 'correct_index', 'sort_order'];

    protected function casts(): array
    {
        return ['options' => 'array'];
    }
}
