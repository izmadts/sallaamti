<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class VolunteerApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Certificate $certificate) {}

    public function build()
    {
        $pdf = Pdf::loadView('certificates.volunteer-id-card', ['certificate' => $this->certificate->load('user')])
            ->setPaper([0, 0, 242.65, 153.07]);

        return $this
            ->subject('You are an approved Sallaamti Volunteer — Your ID Card')
            ->view('emails.volunteers.approved')
            ->attachData($pdf->output(), 'Sallaamti-Volunteer-ID.pdf', ['mime' => 'application/pdf']);
    }
}
