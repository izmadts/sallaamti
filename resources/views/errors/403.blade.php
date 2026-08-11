<x-guest-layout>
    <section class="min-h-[70vh] flex items-center justify-center py-20 bg-cream">
        <div class="max-w-lg mx-auto px-4 text-center">

            <div class="w-24 h-24 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-lg"
                style="background: linear-gradient(135deg, #b8962e, #0d6b6b)">
                🔒
            </div>

            <p class="text-sm font-semibold tracking-widest text-[--teal] uppercase mb-2">Error 403</p>
            <h1 class="text-3xl font-extrabold text-gray-800 mb-3">Access Restricted</h1>
            <p class="text-gray-500 mb-8">{{ $exception->getMessage() ?: "You don't have permission to view this page." }}</p>

            <div class="flex gap-3 justify-center flex-wrap">
                @auth
                <a href="{{ route('dashboard') }}" class="btn-base btn-teal px-6 py-2.5 font-semibold">
                    Go to Dashboard
                </a>
                @else
                <a href="{{ url('/') }}" class="btn-base btn-teal px-6 py-2.5 font-semibold">
                    Back to Home
                </a>
                @endauth
                <a href="{{ url('/contact') }}" class="btn-base btn-gold px-6 py-2.5 font-semibold">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</x-guest-layout>
