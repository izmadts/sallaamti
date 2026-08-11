<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahReport extends Model
{
    protected $fillable = ['reporter_profile_id', 'reported_profile_id', 'nikah_interest_id', 'reason', 'details', 'status'];

    public function reporter()
    {
        return $this->belongsTo(NikahProfile::class, 'reporter_profile_id');
    }

    public function reported()
    {
        return $this->belongsTo(NikahProfile::class, 'reported_profile_id');
    }

    public function interest()
    {
        return $this->belongsTo(NikahInterest::class, 'nikah_interest_id');
    }
}
