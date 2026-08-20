<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkMessageRecipient extends Model
{
    protected $fillable = [
        'bulk_message_id',
        'user_id',
        'subscriber_id',
        'channel_address',
        'status',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function bulkMessage()
    {
        return $this->belongsTo(BulkMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    // Subscriber has no name field (just an email captured off the public
    // /subscribe form), so a subscriber-sourced recipient always falls back
    // to a generic greeting rather than leaving {{name}} visibly unresolved.
    public function recipientName(): string
    {
        return $this->user?->name ?? 'there';
    }
}
