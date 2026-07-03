@extends('emails.layouts.app')

@section('content')

<h2>Donation Verification</h2>

<p>

    Unfortunately we could not verify your payment.

</p>

<p>

    Reason:

</p>

<div style="background:#fff3cd;padding:15px;border-radius:5px;">

    {{ $donation->payment_rejection_reason }}

</div>

<p>

    Please submit your payment again or contact us.

</p>

@endsection