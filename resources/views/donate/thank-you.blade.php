<x-guest-layout>
    <section class="min-h-screen flex items-center justify-center py-20 bg-cream">
        <div class="max-w-lg mx-auto px-4 text-center">

            {{-- Success animation --}}
            <div class="w-24 h-24 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-lg"
                style="background: linear-gradient(135deg, #b8962e, #0d6b6b)">
                🤲
            </div>

            <h1 class="text-3xl font-extrabold text-gray-800 mb-3">JazakAllah Khair!</h1>
            <p class="text-gray-500 mb-2">Your donation has been submitted successfully.</p>
            <p class="text-gray-500 mb-8">Our team will verify and confirm within <strong>24 hours</strong>, in sha Allah.</p>

            {{-- Quran verse --}}
            <div class="rounded-2xl p-6 mb-8" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
                <p class="text-white/90 italic text-base leading-relaxed mb-2">
                    "The example of those who spend their wealth in the way of Allah is like a seed of grain which grows seven spikes; in each spike is a hundred grains."
                </p>
                <p class="text-white/50 text-sm">Surah Al-Baqarah 2:261</p>
            </div>

            {{-- Receipt note --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-6 text-sm text-gray-600">
                <p class="flex items-center gap-2 justify-center mb-1">
                    <i class="fa fa-envelope text-teal-600"></i>
                    A confirmation email has been sent to your inbox.
                </p>
                <p class="text-gray-400 text-xs">Check your spam folder if you don't see it within a few minutes.</p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 justify-center flex-wrap">
                @auth
                <a href="{{ route('donate.my') }}" class="btn-base btn-teal px-6 py-2.5 font-semibold">
                    My Donations
                </a>
                @endauth
                <a href="{{ url('/') }}" class="btn-base btn-gold px-6 py-2.5 font-semibold">
                    Back to Home
                </a>
            </div>

        </div>
    </section>
</x-guest-layout>