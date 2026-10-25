<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// One row per time a staff member (admin/Nikah Counselor) posted this
// profile's share card to an external marketing channel (their WhatsApp
// Channel, Facebook page, etc.) — see the migration's own comment. Purely a
// log for staff visibility; nothing reads this to gate behavior.
class NikahProfileShare extends Model
{
    protected $fillable = ['nikah_profile_id', 'shared_by', 'channel', 'note'];

    public const CHANNELS = [
        'whatsapp_channel' => 'WhatsApp Channel',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'threads' => 'Threads',
        'tiktok' => 'TikTok',
        'twitter' => 'Twitter / X',
        'wall' => 'Sallaamti Wall',
        'other' => 'Other',
    ];

    public function profile()
    {
        return $this->belongsTo(NikahProfile::class, 'nikah_profile_id');
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }
}
