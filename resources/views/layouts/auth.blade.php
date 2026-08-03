<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name', 'Sallaamti'))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

    {{-- Tailwind via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal: #0d6b6b;
            --teal-dark: #095555;
            --teal-light: #e8f5f5;
            --gold: #b8962e;
            --cream: #fdfaf3;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--cream);
            min-height: 100vh;
        }

        /* ===== AUTH WRAPPER ===== */
        .auth-wrapper {
            min-height: 100vh;
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

        /* ===== AUTH FORM ELEMENTS ===== */
        .auth-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .auth-icon {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 13px;
            z-index: 1;
            pointer-events: none;
        }

        .auth-input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2937;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .auth-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 107, 107, 0.1);
        }

        .auth-input-error {
            border-color: #f87171 !important;
        }

        .auth-eye-btn {
            position: absolute;
            right: 12px;
            color: #9ca3af;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 13px;
        }

        .auth-eye-btn:hover {
            color: var(--teal);
        }

        .auth-checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            accent-color: var(--teal);
            cursor: pointer;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .auth-divider span {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }

        /* ===== BUTTONS ===== */
        .btn-base {
            display: inline-block;
            border-radius: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 500;
            line-height: 1.5;
        }

        .btn-teal {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff !important;
        }

        .btn-teal:hover {
            background: var(--teal-dark);
            border-color: var(--teal-dark);
        }

        .btn-gold {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff !important;
        }

        .btn-gold:hover {
            background: #9a7b25;
            border-color: #9a7b25;
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

    {{-- Subtle back to home link --}}
    <a href="{{ url('/') }}"
        class="fixed top-4 left-4 z-50 flex items-center gap-2 text-xs font-medium text-gray-400 hover:text-teal-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Sallaamti
    </a>

    {{ $slot }}

</body>

</html>