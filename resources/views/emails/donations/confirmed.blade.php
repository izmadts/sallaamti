@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.Alhamdulillah!') }}</h2>

<p>{{ __('db.Your donation has been successfully verified.') }}</p>

<p>

    {{ __('db.Amount:') }}
    <strong>

        Rs. {{ number_format($donation->amount,2) }}

    </strong>

</p>

<p>

    {{ __('db.May Allah accept your charity and reward you abundantly.') }}

</p>

@endsection