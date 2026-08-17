<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuaReaction extends Model
{
    protected $fillable = ['dua_request_id', 'user_id'];

    public function duaRequest()
    {
        return $this->belongsTo(DuaRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
