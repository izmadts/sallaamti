<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingTimelineEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['lead_id', 'nikah_profile_id', 'matchmaker_id', 'event_type', 'description', 'meta'];

    protected function casts(): array
    {
        return ['meta' => 'array', 'created_at' => 'datetime'];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function nikahProfile()
    {
        return $this->belongsTo(NikahProfile::class);
    }

    public function matchmaker()
    {
        return $this->belongsTo(User::class, 'matchmaker_id');
    }

    // Single entry point every feature logs through, so a lead's timeline
    // and its (once converted) profile's timeline both see the same event
    // without every caller having to remember to write both rows, and a
    // logging failure never breaks the actual action it's describing.
    public static function log(?Lead $lead, ?NikahProfile $profile, string $eventType, string $description, array $meta = []): void
    {
        try {
            static::create([
                'lead_id' => $lead?->id,
                'nikah_profile_id' => $profile?->id ?? $lead?->nikah_profile_id,
                'matchmaker_id' => auth()->id(),
                'event_type' => $eventType,
                'description' => $description,
                'meta' => $meta ?: null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('MatchmakingTimelineEvent::log failed: ' . $e->getMessage());
        }
    }
}
