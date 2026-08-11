<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahModerationNote extends Model
{
    protected $fillable = ['nikah_profile_id', 'admin_id', 'note'];

    public function profile()
    {
        return $this->belongsTo(NikahProfile::class, 'nikah_profile_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
