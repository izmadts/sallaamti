<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingRequirementItem extends Model
{
    protected $fillable = ['matchmaking_requirement_id', 'requirement_type', 'requirement_value', 'priority', 'weight', 'notes'];

    // The doc's own respectful-language requirement (§10): plain field
    // labels, no "good/bad Muslim" framing.
    public const TYPES = [
        'age_range' => 'Age Range',
        'city' => 'City',
        'country' => 'Country',
        'relocation' => 'Relocation Preference',
        'marital_status' => 'Marital Status',
        'height' => 'Height',
        'education' => 'Education',
        'profession' => 'Profession',
        'income_range' => 'Income Range',
        'children_preference' => 'Children Preference',
        'mother_tongue' => 'Mother Tongue',
        'religious_practice' => 'Religious Practice',
        'sect' => 'Sect',
        'hijab_or_beard' => 'Hijab / Beard',
        'family_type' => 'Family Type',
        'family_expectations' => 'Family Expectations',
        'marriage_timeline' => 'Marriage Timeline',
        'other' => 'Other',
    ];

    public function requirement()
    {
        return $this->belongsTo(MatchmakingRequirement::class, 'matchmaking_requirement_id');
    }
}
