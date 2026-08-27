{{-- resources/views/blog/index.blade.php --}}
<x-guest-layout :title="__('db.Blog')" :description="__('db.Reflections, reminders and articles on Quran, Islamic living and community from Sallaamti.')">

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none;">❖</div>
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow">{{ __('db.Our Blog') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">{{ __('db.Latest Blog') }}</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">
                {{ __('db.Reflections, reminders and articles on Quran, Islamic living and community.') }}
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Blog') }}</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- BLOG GRID --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">{{ __('db.Latest From') }}</span>
                <h2 class="section-title">{{ __('db.Our Blog') }}</h2>
            </div>

            @if ($posts->isEmpty())
            <p class="text-center text-gray-500">{{ __('db.No blog posts yet — check back soon.') }}</p>
            @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                <div class="blog-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 wow fadeIn" data-wow-delay="0.1s">
                    <a href="{{ route('blog.show', $post) }}" class="relative overflow-hidden block">
                        @if ($post->cover_image)
                        <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-48 object-cover" alt="{{ $post->title }}">
                        @else
                        <div class="w-full h-48 flex items-center justify-center text-5xl" style="background: var(--teal-light)">📝</div>
                        @endif
                        <div class="absolute top-0 right-0 text-white text-xs font-semibold px-3 py-2" style="background: var(--teal)">{{ $post->published_at?->format('d M Y') }}</div>
                    </a>
                    <div class="p-6">
                        <div class="flex items-center gap-4 text-xs text-gray-400 mb-3">
                            <span><i class="fas fa-user mr-1"></i> {{ $post->author?->name ?? 'Sallaamti' }}</span>
                            @if ($post->category)
                            <span><i class="fas fa-tag mr-1"></i> {{ $post->category }}</span>
                            @endif
                        </div>
                        <a href="{{ route('blog.show', $post) }}" class="block text-lg font-bold text-gray-800 mb-3 hover:text-teal-700 leading-snug">{{ $post->title }}</a>
                        @if ($post->excerpt)
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ $post->excerpt }}</p>
                        @endif
                        <a href="{{ route('blog.show', $post) }}" class="btn-base btn-teal inline-block text-sm px-5 py-2">{{ __('db.More Details') }}</a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
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
            --teal-dark: #095555;
            --teal-light: #e8f5f5;
            --gold: #b8962e;
            --cream: #fdfaf3;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
        }

        .btn-base {
            display: inline-block;
            border-radius: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 500;
            line-height: 1.5;
        }

        .btn-teal {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff !important;
        }

        .btn-teal:hover {
            background: var(--teal-dark);
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

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .blog-card {
            transition: all 0.3s;
        }

        .blog-card:hover {
            transform: translateY(-4px);
        }

        @media (max-width: 767px) {
            .page-hero {
                min-height: 250px !important;
            }
        }
    </style>

</x-guest-layout>
