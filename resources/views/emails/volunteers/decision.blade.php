@extends('emails.layouts.app')

@section('content')

<h2>Assalamu Alaikum {{ $volunteer->name }}!</h2>

@if ($approved)
<p>Congratulations — your volunteer application has been approved. JazakAllah Khair for choosing to serve the Ummah with Sallaamti.</p>

<p>To receive your official Volunteer ID Card (with your unique volunteer ID and a QR code others can scan to verify your status), create a free Sallaamti account using this same email address ({{ $volunteer->email }}) — your ID card will be issued automatically once you sign up.</p>

<p><a href="{{ route('register') }}">Create your account →</a></p>

<p>Welcome to the team, and JazakAllah Khair for your dedication.</p>
@else
<p>JazakAllah Khair for your interest in volunteering with Sallaamti. After review, we're not able to move forward with your application at this time.</p>

<p>This isn't necessarily final — our volunteer needs change, so please feel free to apply again in the future.</p>
@endif

@endsection
