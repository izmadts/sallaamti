@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.Assalamu Alaikum') }}</h2>

<p>{{ __('db.Dear :name,', ['name' => $donation->donor_name]) }}</p>

<p>{{ __('db.Thank you for your donation.') }}</p>

<p>{{ __('db.We have received your payment submission and our team will verify it shortly.') }}</p>

<table cellpadding="8">
    <tr>
        <td><strong>{{ __('db.Donation Amount') }}</strong></td>
        <td>Rs. {{ number_format($donation->amount,2) }}</td>
    </tr>

    <tr>
        <td><strong>{{ __('db.Reference') }}</strong></td>
        <td>{{ $donation->payment_reference }}</td>
    </tr>
</table>

<p>{{ __('db.JazakAllah Khair for supporting Sallaamti.') }}</p>

@endsection