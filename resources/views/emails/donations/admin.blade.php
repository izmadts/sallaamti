@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.New Donation Received') }}</h2>

<p>{{ __('db.A new donation has been submitted and is awaiting confirmation.') }}</p>

<table cellpadding="8">
    <tr>
        <td><strong>{{ __('db.Donor') }}</strong></td>
        <td>{{ $donation->donor_name }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Email') }}</strong></td>
        <td>{{ $donation->email ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Phone') }}</strong></td>
        <td>{{ $donation->phone ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Amount') }}</strong></td>
        <td>Rs. {{ number_format($donation->amount, 2) }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Payment Method') }}</strong></td>
        <td>{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Reference') }}</strong></td>
        <td>{{ $donation->payment_reference }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Donation Number') }}</strong></td>
        <td>{{ $donation->donation_number }}</td>
    </tr>
</table>

<p>{{ __('db.Please review and confirm this donation from the admin panel.') }}</p>

@endsection
