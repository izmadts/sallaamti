@extends('emails.layouts.app')

@section('content')

<div style="line-height:1.6;color:#333;">{!! $body !!}</div>

<hr style="margin:30px 0 15px;border:none;border-top:1px solid #eee;">

<p style="font-size:12px;color:#999;margin:0;">
    You're receiving this because you're a member of Sallaamti.
    <a href="{{ $unsubscribeUrl }}" style="color:#0f766e;">Unsubscribe from these emails</a>.
</p>

@endsection
