<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadClientMessage extends Model
{
    protected $fillable = ['lead_id', 'sender_id', 'message', 'is_read'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
