<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">

<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
 <meta name="theme-color" content="#0d6b6b">
 <meta name="mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
 <link rel="manifest" href="{{ asset('site.webmanifest') }}">
 <meta name="csrf-token" content="{{ csrf_token() }}">

 <title>{{ $title ? $title . ' — ' . config('app.name') : config('app.name', 'Laravel') }}</title>
 <meta name="description" content="{{ $description ?? setting('site_tagline') }}">
 <link rel="canonical" href="{{ url()->current() }}">
 <meta property="og:type" content="website">
 <meta property="og:url" content="{{ url()->current() }}">
 <meta property="og:title" content="{{ $title ?? config('app.name') }}">
 <meta property="og:description" content="{{ $description ?? setting('site_tagline') }}">
 <meta property="og:image" content="{{ $image ?? asset('images/sallaamti-logo.png') }}">
 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
 <meta name="twitter:description" content="{{ $description ?? setting('site_tagline') }}">
 <meta name="twitter:image" content="{{ $image ?? asset('images/sallaamti-logo.png') }}">
 {{-- Favicon --}}
 <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
 <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
 @include('partials.gtm-head')

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net">
 <link rel="preconnect" href="https://use.fontawesome.com" crossorigin>
 <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

 <!-- Scripts -->
 @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
 @include('partials.gtm-body')
 <div class="min-h-screen bg-gray-100 pb-20 sm:pb-0">
 @include('layouts.navigation')
 @auth
 @include('components.profile-completion-banner')
 @endauth

 <!-- Page Heading -->
 @isset($header)
 <header class="bg-white shadow">
 <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
 {{ $header }}
 </div>
 </header>
 @endisset

 <!-- Page Content -->
 <main>
 @if (session('success') || session('status') || session('error') || session('info') || session('warning'))
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
 <x-flash-messages />
 </div>
 @endif
 {{ $slot }}
 </main>
 </div>
 @include('partials.footer')
</body>

</html>