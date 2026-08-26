@extends('emails.layouts.app')

@section('content')

<h2>{{ __('db.New Contact Message') }}</h2>

<p>{{ __('db.A visitor submitted the contact form on the website.') }}</p>

<table cellpadding="8">
    <tr>
        <td><strong>{{ __('db.Name') }}</strong></td>
        <td>{{ $contactMessage->name }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('db.Email') }}</strong></td>
        <td>{{ $contactMessage->email }}</td>
    </tr>
    @if ($contactMessage->phone)
    <tr>
        <td><strong>{{ __('db.Phone') }}</strong></td>
        <td>{{ $contactMessage->phone }}</td>
    </tr>
    @endif
    <tr>
        <td><strong>{{ __('db.Subject') }}</strong></td>
        <td>{{ $contactMessage->subject }}</td>
    </tr>
</table>

<p><strong>{{ __('db.Message:') }}</strong></p>
<p>{{ $contactMessage->message }}</p>

@endsection
