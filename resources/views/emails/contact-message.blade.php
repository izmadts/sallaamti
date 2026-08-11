@extends('emails.layouts.app')

@section('content')

<h2>New Contact Message</h2>

<p>A visitor submitted the contact form on the website.</p>

<table cellpadding="8">
    <tr>
        <td><strong>Name</strong></td>
        <td>{{ $contactMessage->name }}</td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td>{{ $contactMessage->email }}</td>
    </tr>
    @if ($contactMessage->phone)
    <tr>
        <td><strong>Phone</strong></td>
        <td>{{ $contactMessage->phone }}</td>
    </tr>
    @endif
    <tr>
        <td><strong>Subject</strong></td>
        <td>{{ $contactMessage->subject }}</td>
    </tr>
</table>

<p><strong>Message:</strong></p>
<p>{{ $contactMessage->message }}</p>

@endsection
