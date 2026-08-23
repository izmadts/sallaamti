<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingConsent extends Model
{
    // A recorded moment, not a live toggle — a matchmaker gets consent
    // verbally, over WhatsApp, or in person, then logs it here. Revoking
    // sets revoked_at rather than deleting the row, so the fact that
    // consent WAS once given (and later withdrawn) stays on the record.
    public const TYPES = [
        'matchmaking_participation' => 'Matchmaking Participation — agrees to be shown prospective matches and have their own profile shared with candidates under review',
        'contact_sharing' => 'Contact Sharing — agrees their contact details may be shared once a mutual match is confirmed',
        'photo_sharing' => 'Photo Sharing — agrees their photo may be shared with prospective matches',
    ];

    public const METHODS = [
        'verbal' => 'Verbal (in person)',
        'phone_call' => 'Phone Call',
        'whatsapp' => 'WhatsApp',
        'digital_form' => 'Digital Form',
        'other' => 'Other',
    ];

    protected $fillable = ['lead_id', 'consent_type', 'method', 'notes', 'recorded_by', 'granted_at', 'revoked_at', 'revoked_by'];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at');
    }
}
