{{-- resources/views/blog.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #b8962e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">Knowledge & Insights</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">📰 Sallaamti Blog</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                Articles on Islamic education, parenting, marriage, community and contemporary Muslim life.
            </p>
        </div>
    </section>

    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Coming Soon --}}
            <div class="text-center py-12 mb-12">
                <div class="text-6xl mb-4">✍️</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">Articles Coming Soon</h3>
                <p class="text-gray-500 max-w-lg mx-auto">
                    Our scholars and educators are preparing in-depth articles on Islamic topics. Subscribe to be notified when we publish.
                </p>
            </div>

            {{-- Topics preview --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                ['📖', 'Quran & Tafseer', 'Deep dives into Quranic verses and their meanings for modern Muslims.'],
                ['💑', 'Marriage & Family', 'Practical Islamic guidance on building strong Muslim families.'],
                ['👨‍👩‍👧', 'Parenting', 'Raising children with Islamic values in a modern world.'],
                ['🌍', 'Community', 'Building strong Muslim communities and supporting each other.'],
                ] as $topic)
                <div class="bg-white rounded-2xl p-5 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="text-3xl mb-3">{{ $topic[0] }}</div>
                    <h6 class="font-bold text-gray-800 mb-1">{{ $topic[1] }}</h6>
                    <p class="text-gray-500 text-xs leading-relaxed">{{ $topic[2] }}</p>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <section class="py-14 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="final-cta-title">Want to Contribute?</h2>
            <p class="final-cta-sub">Are you a scholar, educator or writer? We welcome Islamic article contributions.</p>
            <a href="{{ url('/contact') }}" class="btn-base btn-gold px-8 py-3 font-semibold mt-6 inline-block">Get In Touch</a>
        </div>
    </section>
</x-guest-layout>