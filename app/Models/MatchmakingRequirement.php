<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingRequirement extends Model
{
    protected $fillable = ['lead_id', 'nikah_profile_id', 'created_by', 'notes'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function nikahProfile()
    {
        return $this->belongsTo(NikahProfile::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(MatchmakingRequirementItem::class);
    }

    public function mustHaves()
    {
        return $this->items()->where('priority', 'must_have');
    }
}
