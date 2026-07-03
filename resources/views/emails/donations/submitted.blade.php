@extends('emails.layouts.app')

@section('content')

<h2>Assalamu Alaikum</h2>

<p>Dear {{ $donation->donor_name }},</p>

<p>Thank you for your donation.</p>

<p>We have received your payment submission and our team will verify it shortly.</p>

<table cellpadding="8">
    <tr>
        <td><strong>Donation Amount</strong></td>
        <td>Rs. {{ number_format($donation->amount,2) }}</td>
    </tr>

    <tr>
        <td><strong>Reference</strong></td>
        <td>{{ $donation->transaction_reference }}</td>
    </tr>
</table>

<p>JazakAllah Khair for supporting Sallaamti.</p>

@endsection