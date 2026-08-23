<?php

namespace App\Support;

// Resolves the ?module= key a homepage card link carries through
// register/login into the real route for that module's own first
// step. Deliberately a closed whitelist (not an arbitrary redirect
// target) — the only user-controlled input is which of these known
// keys was picked, never a URL itself, so there's no open-redirect risk.
class ModuleRedirects
{
    public const KEYS = ['nikah', 'quran', 'quran_live', 'skills', 'counseling', 'donation', 'volunteer', 'wall'];

    public static function resolve(?string $key): ?string
    {
        if (!in_array($key, self::KEYS, true)) {
            return null;
        }

        return match ($key) {
            'nikah' => route('nikah.create'),
            'quran' => route('courses.index'),
            'quran_live' => route('quran-live.index'),
            'skills' => route('skills.index'),
            'counseling' => route('counseling.book.start'),
            'donation' => route('donate.create'),
            'volunteer' => route('volunteer.create'),
            'wall' => route('wall.index'),
        };
    }
}
