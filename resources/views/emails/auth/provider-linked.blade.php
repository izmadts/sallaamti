@extends('emails.layouts.app')

@section('content')

<h2>Assalamu Alaikum {{ $user->name }}!</h2>

<p>{{ ucfirst($provider) }} was just used to sign in to your Sallaamti account ({{ $user->email }}).</p>

<p>If this was you, no action is needed.</p>

<p><strong>If this wasn't you</strong>, please reset your password immediately and contact our support team.</p>

<p><a href="{{ route('password.request') }}">Reset your password →</a></p>

@endsection
