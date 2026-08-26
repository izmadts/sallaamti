@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.Donation Verification') }}</h2>

<p>

    {{ __('db.Unfortunately we could not verify your payment.') }}

</p>

<p>

    {{ __('db.Reason:') }}

</p>

<div style="background:#fff3cd;padding:15px;border-radius:5px;">

    {{ $donation->payment_rejection_reason }}

</div>

<p>

    {{ __('db.Please submit your payment again or contact us.') }}

</p>

@endsection