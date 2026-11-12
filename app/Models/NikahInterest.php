<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NikahInterest extends Model
{
    // Accepted interest = both sides said yes: their leads become Matched.
    protected static function booted(): void
    {
        static::saved(function (NikahInterest $interest) {
            if ($interest->status !== 'accepted' || (!$interest->wasChanged('status') && !$interest->wasRecentlyCreated)) {
                return;
            }
            try {
                foreach ([$interest->sender_profile_id, $interest->receiver_profile_id] as $pid) {
                    $profile = NikahProfile::find($pid);
                    if (!$profile) {
                        continue;
                    }
                    foreach (Lead::forProfile($profile) as $lead) {
                        if (!in_array($lead->status, Lead::INACTIVE_STATUSES, true) && $lead->status !== 'matched') {
                            $lead->forceFill(['status' => 'matched'])->saveQuietly();
                            MatchmakingTimelineEvent::log($lead, $profile, 'status_changed', 'Status changed automatically to Matched.');
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Lead matched sync failed: ' . $e->getMessage());
            }
        });
    }

    use HasFactory;

    protected $fillable = ['sender_profile_id', 'receiver_profile_id', 'status', 'responded_at'];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(NikahProfile::class, 'sender_profile_id');
    }

    public function receiver()
    {
        return $this->belongsTo(NikahProfile::class, 'receiver_profile_id');
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
    
    public function guardianMessages()
    {
        return $this->hasMany(NikahGuardianMessage::class);
    }
}
