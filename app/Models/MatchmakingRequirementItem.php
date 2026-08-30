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

    // Null means "can't be judged" (no comparable NikahProfile field, or
    // this item has no value yet) — callers must exclude these from a
    // score rather than counting them as a miss, or every candidate would
    // be penalized for requirement types nothing can actually check
    // (income_range, marriage_timeline, family_expectations, relocation,
    // children_preference, other — none of these have a matching column).
    public function matchesCandidate(NikahProfile $candidate): ?bool
    {
        $value = trim((string) $this->requirement_value);
        if ($value === '') {
            return null;
        }

        return match ($this->requirement_type) {
            'age_range' => $this->ageMatches($candidate->age, $value),
            'city' => $this->textMatches($candidate->city, $value),
            'country' => $this->textMatches($candidate->country, $value),
            'marital_status' => $this->textMatches($candidate->marital_status, $value),
            'height' => $this->textMatches($candidate->height, $value),
            'education' => $this->textMatches($candidate->education, $value),
            'profession' => $this->textMatches($candidate->profession, $value),
            'mother_tongue' => $this->textMatches($candidate->language, $value),
            'religious_practice' => $this->textMatches($candidate->prayer_frequency, $value),
            'sect' => $this->textMatches($candidate->sect, $value),
            'hijab_or_beard' => $this->textMatches($candidate->hijab_or_beard, $value),
            'family_type' => $this->textMatches($candidate->family_type, $value),
            default => null,
        };
    }

    // Loose on purpose — a counselor's typed value ("Never Married") and
    // the profile's stored value ("never_married") should still count as
    // the same thing, so this normalizes punctuation/case and accepts a
    // match in either containment direction rather than requiring exact
    // equality.
    private function textMatches(?string $candidateValue, string $requirementValue): bool
    {
        if ($candidateValue === null || $candidateValue === '') {
            return false;
        }

        $normalize = fn (string $s) => trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower($s)));
        $a = $normalize($candidateValue);
        $b = $normalize($requirementValue);

        return $a !== '' && $b !== '' && (str_contains($a, $b) || str_contains($b, $a));
    }

    // Accepts "25-30", "25 to 30", or a single "25+"-style shorthand
    // (treated as a minimum) — counselors type this inconsistently, so
    // this just pulls out whatever numbers are present.
    private function ageMatches(?int $age, string $value): bool
    {
        if ($age === null) {
            return false;
        }

        preg_match_all('/\d+/', $value, $matches);
        $numbers = array_map('intval', $matches[0] ?? []);

        return match (count($numbers)) {
            0 => false,
            1 => $age >= $numbers[0],
            default => $age >= min($numbers[0], $numbers[1]) && $age <= max($numbers[0], $numbers[1]),
        };
    }
}
