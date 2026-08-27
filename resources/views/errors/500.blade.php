<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('db.Something Went Wrong — Sallaamti') }}</title>
    <style>
        :root {
            --teal: #0d6b6b;
            --gold: #b8962e;
            --cream: #fdfaf3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--cream);
            padding: 24px;
        }
        .card {
            max-width: 480px;
            text-align: center;
        }
        .icon {
            width: 96px;
            height: 96px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--gold), var(--teal));
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .eyebrow {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--teal);
            margin: 0 0 8px;
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 12px;
        }
        p {
            color: #6b7280;
            line-height: 1.6;
            margin: 0 0 32px;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
        }
        .btn-teal { background: var(--teal); }
        .btn-gold { background: var(--gold); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <p class="eyebrow">{{ __('db.Error 500') }}</p>
        <h1>{{ __('db.Something Went Wrong') }}</h1>
        <p>{{ __('db.An unexpected error occurred on our end. Our team has been notified — please try again in a few minutes.') }}</p>
        <div class="actions">
            <a href="/" class="btn btn-teal">{{ __('db.Back to Home') }}</a>
            <a href="/contact" class="btn btn-gold">{{ __('db.Contact Us') }}</a>
        </div>
    </div>
</body>
</html>
