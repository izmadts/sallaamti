@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.Assalamu Alaikum :name!', ['name' => $certificate->user->name]) }}</h2>

<p>{{ __('db.Congratulations — your volunteer application has been approved. JazakAllah Khair for choosing to serve the Ummah with Sallaamti.') }}</p>

<p>{{ __('db.Your official Volunteer ID Card is attached to this email as a PDF. It carries your unique volunteer ID and a QR code that anyone can scan to verify your status on our website.') }}</p>

<table cellpadding="8">
    <tr>
        <td><strong>{{ __('db.Volunteer ID') }}</strong></td>
        <td>{{ $certificate->certificate_number }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Issued') }}</strong></td>
        <td>{{ $certificate->issued_at->format('d F Y') }}</td>
    </tr>
</table>

<p>{{ __('db.Welcome to the team, and JazakAllah Khair for your dedication.') }}</p>

@endsection
