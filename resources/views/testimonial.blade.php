{{-- resources/views/testimonial.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">What People Say</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">⭐ Testimonials</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                Real stories from our community — students, parents, and families who found value in Sallaamti.
            </p>
        </div>
    </section>

    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            @php $testimonials = \App\Models\Testimonial::published()->orderBy('order')->get(); @endphp

            <div class="text-center mb-10">
                @auth
                <a href="{{ route('testimonials.create') }}" class="btn-base btn-gold px-6 py-3 font-semibold inline-block">⭐ Share Your Story</a>
                <a href="{{ route('testimonials.mine') }}" class="text-sm text-gray-500 hover:text-[--teal] block mt-2">View your submissions →</a>
                @else
                <a href="{{ route('login') }}" class="btn-base btn-gold px-6 py-3 font-semibold inline-block">⭐ Log In to Share Your Story</a>
                @endauth
            </div>

            @if ($testimonials->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($testimonials as $t)
                <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex gap-1 mb-3">
                        @for ($i = 0; $i < min($t->rating, 5); $i++)
                            <i class="fas fa-star" style="color: var(--gold)"></i>
                            @endfor
                    </div>
                    <p class="text-gray-600 text-sm italic leading-relaxed mb-4">"{{ $t->content }}"</p>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                        <img src="{{ $t->photo ? Storage::url($t->photo) : asset('img/testimonial-1.jpg') }}"
                            loading="lazy" class="w-12 h-12 rounded-full object-cover" alt="{{ $t->name }}">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $t->name }}</p>
                            @if ($t->location)
                            <p class="text-xs text-gray-400">{{ $t->location }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16">
                <div class="text-6xl mb-4">⭐</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Testimonials Coming Soon</h3>
                <p class="text-gray-500">We're collecting stories from our community members.</p>
            </div>
            @endif

            <p class="text-center text-gray-400 text-sm mt-8">
                <i class="fa fa-lock mr-1"></i> Some names and details may be changed to protect privacy.
            </p>
        </div>
    </section>

    <section class="py-14 final-cta-section">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="final-cta-title">Join Thousands of Satisfied Members</h2>
            <p class="final-cta-sub">Start your journey with Sallaamti today — free to join.</p>
            <div class="flex gap-3 justify-center flex-wrap mt-6">
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold px-8 py-3 font-semibold">Register Free</a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-base btn-gold px-8 py-3 font-semibold">Go to Dashboard</a>
                @endguest
            </div>
        </div>
    </section>
</x-guest-layout>