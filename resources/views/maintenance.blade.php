<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('db.:site — Under Maintenance', ['site' => setting('site_name')]) }}</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 80px 20px;
            background: #f8fafc;
            color: #374151;
        }

        h1 {
            font-size: 2rem;
            color: #0d6b6b;
        }

        p {
            color: #6b7280;
            margin-top: 12px;
        }
    </style>
</head>

<body>
    <h1>🌙 سلامتی</h1>
    <h2>{{ __('db.We\'ll be back soon') }}</h2>
    <p>{{ __('db.:site is currently undergoing maintenance.', ['site' => setting('site_name')]) }}</p>
    <p>{{ __('db.Please check back shortly — JazakAllah Khair for your patience.') }}</p>
</body>

</html>