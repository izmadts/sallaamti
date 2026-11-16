<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingQuizAttempt extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['matchmaker_application_id', 'score', 'total', 'passed', 'answers'];

    protected function casts(): array
    {
        return ['passed' => 'boolean', 'answers' => 'array', 'created_at' => 'datetime'];
    }

    public function application()
    {
        return $this->belongsTo(MatchmakerApplication::class, 'matchmaker_application_id');
    }
}
