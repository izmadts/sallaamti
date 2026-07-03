@extends('emails.layouts.app')

@section('content')

<h2>Alhamdulillah!</h2>

<p>Your donation has been successfully verified.</p>

<p>

    Amount:
    <strong>

        Rs. {{ number_format($donation->amount,2) }}

    </strong>

</p>

<p>

    May Allah accept your charity and reward you abundantly.

</p>

@endsection