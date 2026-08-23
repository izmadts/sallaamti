<?php

namespace App\Services\Matchmaking;

use App\Models\MatchmakingRequirement;
use App\Models\NikahProfile;
use Illuminate\Support\Collection;

// Turns a client's captured Requirements (see
// Matchmaker\ClientController::saveRequirement) into a ranked candidate
// list — the missing link between "we know what they want" and "here's
// who actually fits", instead of the matchmaker eyeballing every search
// result by hand. Deliberately simple and explainable (plain weighted
// matching against structured profile fields, not a black box) — a human
// still makes the final call on every proposal, this just narrows the pool.
class CompatibilityScorer
{
    private const WEIGHTS = [
        'must_have' => 3,
        'preferred' => 2,
        'flexible' => 1,
    ];

    // Null means "don't show this candidate at all" — either they fail a
    // must-have, or none of the saved requirements were things this scorer
    // can actually check against a profile (free-text ones like "family
    // expectations" are skipped, not guessed at).
    public function score(MatchmakingRequirement $requirement, NikahProfile $candidate): ?array
    {
        $matched = collect();
        $unmatched = collect();
        $earned = 0;
        $possible = 0;

        foreach ($requirement->items as $item) {
            $isMatch = $this->evaluate($item->requirement_type, $item->requirement_value, $candidate);

            if ($isMatch === null) {
                continue;
            }

            if ($item->priority === 'must_have' && !$isMatch) {
                return null;
            }

            $weight = self::WEIGHTS[$item->priority] ?? 1;
            $possible += $weight;

            if ($isMatch) {
                $earned += $weight;
                $matched->push($item);
            } else {
                $unmatched->push($item);
            }
        }

        if ($possible === 0) {
            return null;
        }

        return [
            'score' => (int) round($earned / $possible * 100),
            'matched' => $matched,
            'unmatched' => $unmatched,
        ];
    }

    // Highest-scoring first. Candidates that can't be scored (or fail a
    // must-have) are left out entirely rather than shown with a
    // meaningless 0%.
    public function rank(MatchmakingRequirement $requirement, Collection $candidates): Collection
    {
        return $candidates
            ->map(fn (NikahProfile $candidate) => ['profile' => $candidate, 'result' => $this->score($requirement, $candidate)])
            ->filter(fn ($row) => $row['result'] !== null)
            ->sortByDesc(fn ($row) => $row['result']['score'])
            ->values();
    }

    private function evaluate(string $type, string $value, NikahProfile $candidate): ?bool
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return match ($type) {
            'age_range' => $this->matchAgeRange($value, $candidate->age),
            'city' => $this->matchContains($value, $candidate->city),
            'country' => $this->matchContains($value, $candidate->country),
            'marital_status' => $this->matchNormalized($value, $candidate->marital_status),
            'height' => $this->matchContains($value, $candidate->height),
            'education' => $this->matchContains($value, $candidate->education),
            'profession' => $this->matchContains($value, $candidate->profession),
            'sect' => $this->matchContains($value, $candidate->sect),
            'religious_practice' => $this->matchContains($value, $candidate->prayer_frequency),
            'hijab_or_beard' => $this->matchNormalized($value, $candidate->hijab_or_beard),
            'family_type' => $this->matchContains($value, $candidate->family_type),
            'mother_tongue' => $this->matchContains($value, $candidate->language),
            'children_preference' => $this->matchChildrenPreference($value, $candidate),
            // relocation, income_range, family_expectations, marriage_timeline,
            // other — free-text or not tracked on the profile at all.
            default => null,
        };
    }

    private function matchContains(string $value, ?string $field): ?bool
    {
        if (!$field) {
            return null;
        }

        return str_contains(strtolower($field), strtolower($value)) || str_contains(strtolower($value), strtolower($field));
    }

    private function matchNormalized(string $value, ?string $field): ?bool
    {
        if (!$field) {
            return null;
        }

        $normalize = fn ($s) => strtolower(str_replace([' ', '-'], '_', trim($s)));

        return $normalize($value) === $normalize($field);
    }

    private function matchAgeRange(string $value, ?int $age): ?bool
    {
        if (!$age) {
            return null;
        }

        if (preg_match('/(\d{1,2})\s*-\s*(\d{1,2})/', $value, $m)) {
            return $age >= (int) $m[1] && $age <= (int) $m[2];
        }

        if (preg_match('/(\d{1,2})/', $value, $m)) {
            return abs($age - (int) $m[1]) <= 2;
        }

        return null;
    }

    private function matchChildrenPreference(string $value, NikahProfile $candidate): ?bool
    {
        $value = strtolower($value);
        $hasChildren = (bool) $candidate->has_children;

        if (str_contains($value, 'no ') || str_contains($value, 'without') || str_contains($value, "don't") || str_contains($value, 'not have')) {
            return !$hasChildren;
        }

        if (str_contains($value, 'open') || str_contains($value, 'okay') || str_contains($value, 'fine') || str_contains($value, 'yes')) {
            return true;
        }

        return null;
    }
}
