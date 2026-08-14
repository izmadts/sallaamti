<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name', 'Sallaamti'))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://use.fontawesome.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

    {{-- Tailwind via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--
        Shared tokens (--teal, --gold, ...) and .auth-*/.btn-* component
        classes now live in resources/css/app.css so pages under both
        this layout and layouts/app.blade.php render identically. Only
        this page's own wrapper/card chrome stays here.
    --}}
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--cream);
            min-height: 100vh;
        }

        /* ===== AUTH TOPBAR ===== */
        .auth-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            background: rgba(253, 250, 243, 0.9);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid rgba(13, 107, 107, 0.08);
        }

        .auth-topbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            min-width: 0;
        }

        .auth-topbar-brand img {
            height: 32px;
            width: auto;
            flex-shrink: 0;
        }

        .auth-topbar-brand span {
            font-weight: 700;
            font-size: 1rem;
            color: var(--teal);
            white-space: nowrap;
        }

        /* ===== AUTH WRAPPER ===== */
        .auth-wrapper {
            min-height: calc(100vh - 62px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            background: linear-gradient(135deg, #e8f5f5 0%, #fdfaf3 50%, #fdf6e3 100%);
            position: relative;
        }

        .auth-wrapper::before {
            content: '❖';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40rem;
            color: rgba(13, 107, 107, 0.03);
            pointer-events: none;
            z-index: 0;
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            padding: 48px 40px;
            position: relative;
            z-index: 1;
        }

        /* ===== MOBILE ===== */
        @media (max-width: 575px) {
            .auth-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="auth-topbar">
        <a href="{{ url('/') }}" class="auth-topbar-brand">
            <img src="{{ asset('img/logo.png') }}" alt="{{ setting('site_name', 'Sallaamti') }}">
            <span>{{ setting('site_name', 'Sallaamti') }}</span>
        </a>

        <x-language-switcher />
    </div>

    {{ $slot }}

</body>

</html>