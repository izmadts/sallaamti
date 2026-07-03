@extends('emails.layouts.app')

@section('content')

<h2>Assalamu Alaikum</h2>

<p>Thank you for subscribing to the Sallaamti Newsletter.</p>

<p>Please verify your email by clicking the button below.</p>

<p style="text-align:center;margin:30px 0;">

    <a href="{{ route('newsletter.verify', $subscriber->verification_token) }}"
        style="background:#0f766e;color:#fff;text-decoration:none;padding:14px 30px;border-radius:6px;display:inline-block;">

        Verify My Email

    </a>

</p>

<p>If you did not subscribe, you may ignore this email.</p>

<p>JazakAllah Khair.</p>

@endsection