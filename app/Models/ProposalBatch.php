<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalBatch extends Model
{
    protected $fillable = ['lead_id', 'nikah_profile_id', 'matchmaker_id', 'batch_number', 'status', 'notes', 'sent_at'];

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

    public function matchmaker()
    {
        return $this->belongsTo(User::class, 'matchmaker_id');
    }

    public function proposals()
    {
        return $this->hasMany(MatchProposal::class);
    }

    public function respondedCount(): int
    {
        return $this->proposals()->where('status', 'responded')->count();
    }
}
