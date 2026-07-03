<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterSubscribed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber
    ) {}

    public function build()
    {
        return $this
            ->subject('Verify Your Newsletter Subscription')
            ->view('emails.newsletter.subscribed');
    }
}
