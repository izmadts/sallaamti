{{-- resources/views/events.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">What's Happening</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">Events & Programs</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                Workshops, seminars, live sessions and community gatherings — join us and grow together.
            </p>
        </div>
    </section>

    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center py-20">
                <div class="text-6xl mb-4">📅</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">Events Coming Soon</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    We're planning exciting workshops, seminars and community events. Register now to be notified when they're announced.
                </p>
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-teal px-8 py-3 font-semibold">
                    Register to Get Notified
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-base btn-teal px-8 py-3 font-semibold">
                    Go to Dashboard
                </a>
                @endguest
            </div>

            {{-- Upcoming Types --}}
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ([
                ['📖', 'Quran Workshops', 'Intensive Quran recitation and Tajweed workshops with expert teachers.', 'var(--teal)'],
                ['🕌', 'Islamic Seminars', 'Monthly online and in-person seminars on contemporary Islamic topics.', '#b8962e'],
                ['👨‍👩‍👧', 'Family Programs', 'Parenting workshops, marriage enrichment programs and family counseling sessions.', '#7d3c98'],
                ] as $evt)
                <div class="bg-white rounded-2xl p-6 shadow-sm text-center border-t-4" style="border-color: {{ $evt[3] }}">
                    <div class="text-4xl mb-3">{{ $evt[0] }}</div>
                    <h5 class="font-bold text-gray-800 mb-2">{{ $evt[1] }}</h5>
                    <p class="text-gray-500 text-sm">{{ $evt[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="final-cta-title">Want to Host or Suggest an Event?</h2>
            <p class="final-cta-sub">We welcome community proposals for workshops and seminars.</p>
            <div class="flex gap-3 justify-center flex-wrap mt-6">
                <a href="{{ url('/contact') }}" class="btn-base btn-gold px-8 py-3 font-semibold">Contact Us</a>
                <a href="{{ route('volunteer.create') }}" class="btn-base btn-outline-light px-8 py-3">Volunteer as Speaker</a>
            </div>
        </div>
    </section>
</x-guest-layout>