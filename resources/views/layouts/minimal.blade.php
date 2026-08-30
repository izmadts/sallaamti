<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}" translate="no">

<head>
 <meta charset="utf-8">
 <meta name="google" content="notranslate">
 <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
 <meta name="theme-color" content="#0d6b6b">
 <meta name="mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
 <link rel="manifest" href="{{ asset('site.webmanifest') }}">
 <meta name="csrf-token" content="{{ csrf_token() }}">

 {{-- No SEO/OG/structured-data block here on purpose — every page using
      this layout is a private, signed link (a specific client's progress,
      a specific counselor's application) that should never be indexed,
      previewed, or shared as a generic card. --}}
 <title>{{ $title ?? setting('site_name', 'Sallaamti') }}</title>
 <meta name="robots" content="noindex, nofollow">

 <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
 <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

 <link rel="preconnect" href="https://fonts.bunny.net">
 <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

 @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans bg-cream min-h-screen flex flex-col">

 {{-- Header — logo only. No nav, no login/register, no language switcher:
      this page is a one-task destination someone reaches via a link sent
      to them, not a page they're meant to explore from. Both languages
      already show inline throughout the page content itself. --}}
 <div class="w-full border-b bg-white">
     <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-center">
         <a href="{{ url('/') }}" class="shrink-0 flex items-center">
             <x-application-logo class="h-8 w-auto" />
         </a>
     </div>
 </div>

 <main class="flex-1 w-full">
     {{ $slot }}
 </main>

 {{-- Footer — a support line, nothing to click away to. --}}
 <div class="w-full border-t bg-white py-4">
     <div class="max-w-2xl mx-auto px-4 text-center text-xs text-gray-400 space-y-1">
         <p>{{ __('db.Need help? Contact your Nikah Counselor, or Sallaamti support:') }} {{ setting('social_whatsapp', setting('site_phone')) }}</p>
         <p dir="rtl">مدد درکار ہے؟ اپنے نکاح مشیر سے رابطہ کریں، یا سلامتی سپورٹ: {{ setting('social_whatsapp', setting('site_phone')) }}</p>
         <p>&copy; {{ now()->year }} {{ setting('site_name', 'Sallaamti') }}</p>
     </div>
 </div>

</body>

</html>
