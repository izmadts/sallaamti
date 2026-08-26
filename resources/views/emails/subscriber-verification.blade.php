<!DOCTYPE html>

<html>

<head>

    <title>{{ __('db.Sallaamti Newsletter') }}</title>

</head>

<body>

    <h2>{{ __('db.Assalamu Alaikum') }}</h2>

    <p>

        {{ __('db.Thank you for subscribing to Sallaamti.') }}

    </p>

    <p>

        {{ __('db.Please click below to verify your email.') }}

    </p>

    <p>

        <a href="{{ route('subscriber.verify',$subscriber->verification_token) }}">

            {{ __('db.Verify Subscription') }}

        </a>

    </p>

    <p>

        {{ __('db.JazakAllahu Khairah') }}

    </p>

    <p style="font-size: 12px; color: #888;">

        <a href="{{ route('subscriber.unsubscribe', $subscriber->unsubscribe_token) }}" style="color: #888;">{{ __('db.Unsubscribe') }}</a>

    </p>

</body>

</html>
