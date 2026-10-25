<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Sibling to NikahReport — see the create_lead_message_reports_table
// migration's comment for why this isn't just a column added there.
class LeadMessageReport extends Model
{
    protected $fillable = ['lead_id', 'reporter_user_id', 'reported_user_id', 'lead_client_message_id', 'reason', 'details', 'status'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reported()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function message()
    {
        return $this->belongsTo(LeadClientMessage::class, 'lead_client_message_id');
    }
}
