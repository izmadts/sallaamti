{{-- resources/views/team.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">The People Behind Sallaamti</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">Our Team</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                Dedicated volunteers, qualified teachers and passionate individuals serving the Muslim Ummah.
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">Home</a>
                <span>/</span>
                <a href="{{ url('/about') }}" class="hover:text-white">About</a>
                <span>/</span>
                <span class="text-white">Team</span>
            </nav>
        </div>
    </section>

    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Leadership --}}
            <div class="text-center mb-10">
                <span class="section-eyebrow">Leadership</span>
                <h2 class="section-title">Guiding Sallaamti's Vision</h2>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 mb-16 items-center max-w-4xl mx-auto">
                <div class="lg:col-span-4">
                    <img src="{{ asset('img/team-1.jpg') }}" class="w-full rounded-2xl shadow-lg" alt="Founder">
                </div>
                <div class="lg:col-span-8">
                    <span class="section-eyebrow">Founder & Director</span>
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-1">Mubashar Ahmed</h2>
                    <p class="italic mb-4" style="color: var(--teal)">Founder, Sallaamti</p>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        With a deep passion for Islamic education and community service, Mubashar founded Sallaamti with the vision of creating a platform where every Muslim can access quality Quranic education, find a halal spouse, and build a life aligned with the Quran and Sunnah.
                    </p>
                    <p class="text-gray-600 italic mb-4">
                        "اقرأ، افهم، وطبّق — Read, Understand, and Implement."
                    </p>
                    <div class="flex gap-2">
                        @foreach ([
                        ['fab fa-facebook-f', setting('social_facebook', '#')],
                        ['fab fa-instagram', setting('social_instagram', '#')],
                        ['fab fa-whatsapp', 'https://wa.me/'.setting('social_whatsapp')],
                        ] as $s)
                        <a href="{{ $s[1] }}" target="_blank" class="social-btn"><i class="{{ $s[0] }}"></i></a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Team Grid --}}
            <div class="text-center mb-10">
                <span class="section-eyebrow">Our Team</span>
                <h2 class="section-title">Teachers, Counselors & Volunteers</h2>
            </div>

            @php
            $team = \App\Models\User::role('teacher')->take(8)->get();
            @endphp

            @if ($team->count() > 0)
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($team as $member)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow hover:-translate-y-1 duration-300">
                    <img src="{{ $member->avatarUrl() }}" class="w-full h-52 object-cover" alt="{{ $member->name }}">
                    <div class="p-4 text-center">
                        <h5 class="font-bold text-gray-800">{{ $member->name }}</h5>
                        <p class="text-sm font-semibold mt-0.5" style="color: var(--teal)">Quran Teacher</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10">
                <div class="text-4xl mb-3">👩‍🏫</div>
                <p class="text-gray-500">Our teacher profiles are being set up. Check back soon.</p>
            </div>
            @endif

        </div>
    </section>

    <section class="py-16 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="final-cta-title">Join Our Team</h2>
            <p class="final-cta-sub">Whether you're a teacher, counselor, developer or organizer — your skills are needed.</p>
            <a href="{{ route('volunteer.create') }}" class="btn-base btn-gold px-8 py-3 font-semibold mt-6 inline-block">
                Apply as Volunteer →
            </a>
        </div>
    </section>
</x-guest-layout>