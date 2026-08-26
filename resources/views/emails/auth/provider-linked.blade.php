@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.Assalamu Alaikum :name!', ['name' => $user->name]) }}</h2>

<p>{{ __('db.:provider was just used to sign in to your Sallaamti account (:email).', ['provider' => ucfirst($provider), 'email' => $user->email]) }}</p>

<p>{{ __('db.If this was you, no action is needed.') }}</p>

<p><strong>{{ __("db.If this wasn't you") }}</strong>{{ __('db., please reset your password immediately and contact our support team.') }}</p>

<p><a href="{{ route('password.request') }}">{{ __('db.Reset your password →') }}</a></p>

@endsection
