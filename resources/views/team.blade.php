{{-- resources/views/team.blade.php --}}
<x-guest-layout title="Our Team" description="Meet the dedicated volunteers and professionals behind Sallaamti.">

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none;">❖</div>
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow">Our People</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">Meet Our Team</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">
                Dedicated volunteers and professionals committed to serving the Ummah.
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">Home</a>
                <span>/</span>
                <span class="text-white">Team</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TEAM GRID --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            @if ($teamMembers->isEmpty())
            <p class="text-center text-gray-500">Team information coming soon.</p>
            @else
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($teamMembers as $member)
                <div class="team-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="relative overflow-hidden">
                        @if ($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" class="w-full h-52 object-cover" alt="{{ $member->name }}">
                        @else
                        <div class="w-full h-52 flex items-center justify-center text-5xl" style="background: var(--teal-light)">👤</div>
                        @endif
                    </div>
                    <div class="p-4 text-center">
                        <h5 class="font-bold text-gray-800 mb-0">{{ $member->name }}</h5>
                        <p class="text-sm font-semibold mb-2" style="color: var(--teal)">{{ $member->role }}</p>
                        @if ($member->bio)
                        <div class="prose prose-sm max-w-none text-gray-500">{!! $member->bio !!}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- STYLES --}}
    {{-- ============================================================ --}}
    <style>
        :root {
            --teal: #0d6b6b;
            --teal-light: #e8f5f5;
            --gold: #b8962e;
            --text-dark: #1a1a2e;
        }

        .section-eyebrow {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            display: block;
            margin-bottom: 8px;
        }

        .team-card {
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-4px);
        }

        @media (max-width: 767px) {
            .page-hero {
                min-height: 250px !important;
            }
        }
    </style>

</x-guest-layout>
