<?php

namespace App\Mail;

use App\Models\VolunteerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Covers the two cases VolunteerApproved (which needs a real Certificate,
// and therefore a registered User) can't: an approved GUEST applicant with
// no account to attach an ID card to, and any rejection at all — QA
// finding: neither of these sent the applicant any notification before.
class VolunteerApplicationDecision extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public VolunteerApplication $volunteer, public bool $approved) {}

    public function build()
    {
        return $this
            ->subject($this->approved
                ? 'You are an approved Sallaamti Volunteer!'
                : 'Update on your Sallaamti Volunteer application')
            ->view('emails.volunteers.decision');
    }
}
