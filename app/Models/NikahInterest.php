<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NikahInterest extends Model
{
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
}
