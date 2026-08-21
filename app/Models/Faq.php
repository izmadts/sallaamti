<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    protected $fillable = [
        'module',
        'question_en',
        'answer_en',
        'question_ur',
        'answer_ur',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Single source of truth for which module keys exist — used by the
    // admin form's dropdown and by every <x-faq-section module="..."/>
    // placement, so a typo'd module key can never silently orphan an FAQ.
    public const MODULES = [
        'general' => 'General / Getting Started',
        'nikah' => 'Nikah Matchmaking',
        'quran' => 'Quran Courses',
        'quran_live' => 'Live Quran Classes',
        'skills' => 'Digital Skills',
        'counseling' => 'Family Counseling',
        'donation' => 'Donations',
        'volunteer' => 'Volunteering',
        'wall' => 'Sallaamti Wall',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    public function question(string $locale): string
    {
        if ($locale === 'ur' && filled($this->question_ur)) {
            return $this->question_ur;
        }

        return $this->question_en;
    }

    public function answer(string $locale): string
    {
        if ($locale === 'ur' && filled($this->answer_ur)) {
            return $this->answer_ur;
        }

        return $this->answer_en;
    }
}
