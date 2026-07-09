@extends('emails.layouts.app')

@section('content')

<h2>New Donation Received</h2>

<p>A new donation has been submitted and is awaiting confirmation.</p>

<table cellpadding="8">
    <tr>
        <td><strong>Donor</strong></td>
        <td>{{ $donation->donor_name }}</td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td>{{ $donation->email ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>Phone</strong></td>
        <td>{{ $donation->phone ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>Amount</strong></td>
        <td>Rs. {{ number_format($donation->amount, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Payment Method</strong></td>
        <td>{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</td>
    </tr>
    <tr>
        <td><strong>Reference</strong></td>
        <td>{{ $donation->payment_reference }}</td>
    </tr>
    <tr>
        <td><strong>Donation Number</strong></td>
        <td>{{ $donation->donation_number }}</td>
    </tr>
</table>

<p>Please review and confirm this donation from the admin panel.</p>

@endsection
