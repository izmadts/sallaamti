@extends('emails.layouts.app')

@section('content')

<h2>New Donation Received</h2>

<table cellpadding="8">

    <tr>

        <td>Name</td>

        <td>{{ $donation->donor_name }}</td>

    </tr>

    <tr>

        <td>Email</td>

        <td>{{ $donation->email }}</td>

    </tr>

    <tr>

        <td>Amount</td>

        <td>Rs. {{ number_format($donation->amount,2) }}</td>

    </tr>

    <tr>

        <td>Reference</td>

        <td>{{ $donation->transaction_reference }}</td>

    </tr>

</table>

@endsection