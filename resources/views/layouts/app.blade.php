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
 <meta name="vapid-public-key" content="{{ config('services.webpush.public_key') }}">

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

 {{-- Trix rich text editor (member-facing forms) — no file attachments: there's
 no upload endpoint wired for public submissions, so drops are blocked below. --}}
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.css">
 <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.umd.min.js"></script>
 <style>
     trix-toolbar .trix-button-group--file-tools {
         display: none;
     }

     trix-toolbar .trix-button-row {
         flex-wrap: wrap;
     }

     trix-toolbar .trix-button {
         border-color: #d1d5db;
     }

     trix-toolbar .trix-button.trix-active {
         background: #0d6b6b;
         color: #fff;
     }

     trix-editor {
         border: 1px solid #d1d5db;
         border-radius: 0 0 0.5rem 0.5rem;
         min-height: 8rem;
         padding: 0.75rem;
     }

     trix-toolbar {
         border: 1px solid #d1d5db;
         border-bottom: none;
         border-radius: 0.5rem 0.5rem 0 0;
         background: #f9fafb;
         padding: 0.375rem;
     }
 </style>
 <script>
     document.addEventListener('trix-file-accept', function (event) {
         event.preventDefault();
     });
 </script>
</head>

<body class="font-sans antialiased">
 @include('partials.gtm-body')
 <div class="min-h-screen bg-gray-100 pb-20 sm:pb-0">
 @include('layouts.navigation')
 @auth
 @include('components.profile-completion-banner')
 @include('components.email-binding-reminder')
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
 @if (isset($errors) && $errors->any())
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
 <x-alert type="error">
 <strong>{{ __('db.Please fix the following:') }}</strong>
 <ul class="list-disc ms-5 mt-1">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </x-alert>
 </div>
 @endif
 @if (session('success') || session('status') || session('error') || session('info') || session('warning'))
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
 <x-flash-messages />
 </div>
 @endif
 {{ $slot }}
 </main>
 </div>
 @include('partials.footer')
 @include('components.pwa-install-banner')
</body>

</html>