<x-guest-layout>
    <section class="min-h-[70vh] flex items-center justify-center py-20 bg-cream">
        <div class="max-w-lg mx-auto px-4 text-center">

            <div class="w-24 h-24 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-lg"
                style="background: linear-gradient(135deg, #b8962e, #0d6b6b)">
                🧭
            </div>

            <p class="text-sm font-semibold tracking-widest text-[--teal] uppercase mb-2">{{ __('db.Error 404') }}</p>
            <h1 class="text-3xl font-extrabold text-gray-800 mb-3">{{ __('db.Page Not Found') }}</h1>
            <p class="text-gray-500 mb-8">{{ __("db.We couldn't find the page you were looking for. It may have moved, or the link might be out of date.") }}</p>

            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ url('/') }}" class="btn-base btn-teal px-6 py-2.5 font-semibold">
                    {{ __('db.Back to Home') }}
                </a>
                <a href="{{ url('/contact') }}" class="btn-base btn-gold px-6 py-2.5 font-semibold">
                    {{ __('db.Contact Us') }}
                </a>
            </div>
        </div>
    </section>
</x-guest-layout>
