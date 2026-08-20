<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahContactRequest extends Model
{
    protected $fillable = [
        'nikah_profile_id',
        'requested_by',
        'status',
        'admin_notes',
        'decided_by',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'nikah_profile_id' => 'integer',
            'requested_by' => 'integer',
            'decided_by' => 'integer',
            'decided_at' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->belongsTo(NikahProfile::class, 'nikah_profile_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
