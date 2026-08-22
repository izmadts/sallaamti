<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadShortlistItem extends Model
{
    protected $fillable = ['lead_id', 'nikah_profile_id', 'note', 'sent_at', 'created_by'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

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
}
