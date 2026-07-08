<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'email', 
        'phone', 
        'city', 
        'area_of_interest', 
        'message', 
        'status'
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
