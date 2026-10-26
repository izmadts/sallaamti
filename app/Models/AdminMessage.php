<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A member's support conversation with the Sallaamti team — one shared
// thread per member (not per admin), same "team inbox" convention as
// LeadClientMessage. Any admin can see and reply; sender_id just records
// which specific account (the member, or whichever admin answered) sent a
// given message.
class AdminMessage extends Model
{
    protected $fillable = ['user_id', 'sender_id', 'body', 'is_read'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isFromMember(): bool
    {
        return (int) $this->sender_id === (int) $this->user_id;
    }
}
