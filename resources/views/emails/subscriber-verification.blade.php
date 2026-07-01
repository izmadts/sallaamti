<!DOCTYPE html>

<html>

<head>

    <title>Sallaamti Newsletter</title>

</head>

<body>

    <h2>Assalamu Alaikum</h2>

    <p>

        Thank you for subscribing to Sallaamti.

    </p>

    <p>

        Please click below to verify your email.

    </p>

    <p>

        <a href="{{ route('subscriber.verify',$subscriber->verification_token) }}">

            Verify Subscription

        </a>

    </p>

    <p>

        JazakAllahu Khairah

    </p>

</body>

</html>
