<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahGuardianMessage extends Model
{
    protected $fillable = ['nikah_interest_id', 'sender_id', 'message', 'is_read'];

    public function interest()
    {
        return $this->belongsTo(NikahInterest::class, 'nikah_interest_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
