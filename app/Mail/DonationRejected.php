<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation) {}

    public function build()
    {
        return $this
            ->subject('Donation Verification Update')
            ->view('emails.donations.rejected');
    }
}
