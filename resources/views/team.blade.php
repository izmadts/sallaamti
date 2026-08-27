{{-- resources/views/team.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.The People Behind Sallaamti') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">{{ __('db.Our Team') }}</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                {{ __('db.Dedicated volunteers, qualified teachers and passionate individuals serving the Muslim Ummah.') }}
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <a href="{{ url('/about') }}" class="hover:text-white">{{ __('db.About') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Team') }}</span>
            </nav>
        </div>
    </section>

    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">

            @php
            $founder = $teamMembers->firstWhere('is_founder', true);
            $otherMembers = $teamMembers->reject(fn ($m) => $m->is_founder);
            @endphp

            @if ($founder)
            {{-- Leadership --}}
            <div class="text-center mb-10">
                <span class="section-eyebrow">{{ __('db.Leadership') }}</span>
                <h2 class="section-title">{{ __('db.Guiding Sallaamti\'s Vision') }}</h2>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 mb-16 items-center max-w-4xl mx-auto">
                <div class="lg:col-span-4">
                    <div class="w-full aspect-square rounded-2xl shadow-lg overflow-hidden bg-white flex items-center justify-center">
                        @if ($founder->photo)
                        <img src="{{ Storage::url($founder->photo) }}" loading="lazy" class="w-full h-full object-contain" alt="{{ $founder->name }}">
                        @else
                        <div class="text-6xl">👤</div>
                        @endif
                    </div>
                </div>
                <div class="lg:col-span-8">
                    <span class="section-eyebrow">{{ $founder->role }}</span>
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $founder->name }}</h2>
                    @if ($founder->bio)
                    <div class="prose prose-sm max-w-none text-gray-600 mb-4 leading-relaxed">{!! $founder->bio !!}</div>
                    @endif
                    <div class="flex gap-2">
                        @if ($founder->facebook_url)
                        <a href="{{ $founder->facebook_url }}" target="_blank" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if ($founder->instagram_url)
                        <a href="{{ $founder->instagram_url }}" target="_blank" class="social-btn"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if ($founder->tiktok_url)
                        <a href="{{ $founder->tiktok_url }}" target="_blank" class="social-btn"><i class="fab fa-tiktok"></i></a>
                        @endif
                        @if ($founder->whatsapp_number)
                        <a href="{{ whatsapp_link(null, $founder->whatsapp_number) }}" target="_blank" class="social-btn"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Team Grid --}}
            <div class="text-center mb-10">
                <span class="section-eyebrow">{{ __('db.Our Team') }}</span>
                <h2 class="section-title">{{ __('db.Teachers, Counselors & Volunteers') }}</h2>
            </div>

            @if ($otherMembers->isNotEmpty())
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($otherMembers as $member)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow hover:-translate-y-1 duration-300">
                    <div class="w-full aspect-square bg-gray-50 flex items-center justify-center">
                        @if ($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" loading="lazy" class="w-full h-full object-contain" alt="{{ $member->name }}">
                        @else
                        <div class="text-4xl">👤</div>
                        @endif
                    </div>
                    <div class="p-4 text-center">
                        <h5 class="font-bold text-gray-800">{{ $member->name }}</h5>
                        <p class="text-sm font-semibold mt-0.5" style="color: var(--teal)">{{ $member->role }}</p>
                        <div class="flex justify-center gap-2 mt-2">
                            @if ($member->facebook_url)
                            <a href="{{ $member->facebook_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if ($member->instagram_url)
                            <a href="{{ $member->instagram_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if ($member->tiktok_url)
                            <a href="{{ $member->tiktok_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-tiktok"></i></a>
                            @endif
                            @if ($member->whatsapp_number)
                            <a href="{{ whatsapp_link(null, $member->whatsapp_number) }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-whatsapp"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10">
                <div class="text-4xl mb-3">👩‍🏫</div>
                <p class="text-gray-500">{{ __('db.Our team profiles are being set up. Check back soon.') }}</p>
            </div>
            @endif

        </div>
    </section>

    <section class="py-16 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="final-cta-title">{{ __('db.Join Our Team') }}</h2>
            <p class="final-cta-sub">{{ __('db.Whether you\'re a teacher, counselor, developer or organizer — your skills are needed.') }}</p>
            <a href="{{ route('volunteer.create') }}" class="btn-base btn-gold px-8 py-3 font-semibold mt-6 inline-block">
                {{ __('db.Apply as Volunteer →') }}
            </a>
        </div>
    </section>
</x-guest-layout>