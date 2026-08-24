<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchProposal extends Model
{
    protected $fillable = [
        'proposal_batch_id', 'candidate_profile_id', 'match_reasons', 'internal_notes',
        'status', 'response', 'nikah_interest_id', 'sent_at', 'viewed_at', 'responded_at', 'link_token',
    ];

    protected function casts(): array
    {
        return [
            'match_reasons' => 'array',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function batch()
    {
        return $this->belongsTo(ProposalBatch::class, 'proposal_batch_id');
    }

    public function candidate()
    {
        return $this->belongsTo(NikahProfile::class, 'candidate_profile_id');
    }

    public function nikahInterest()
    {
        return $this->belongsTo(NikahInterest::class);
    }
}
