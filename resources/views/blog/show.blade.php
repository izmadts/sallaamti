{{-- resources/views/blog/show.blade.php --}}
<x-guest-layout :title="$post->title" :description="$post->excerpt ?? Str::limit(strip_tags($post->content), 155)" :image="$post->cover_image ? Storage::url($post->cover_image) : null">

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 260px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none;">❖</div>
        <div class="max-w-4xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            @if ($post->category)
            <span class="section-eyebrow">{{ $post->category }}</span>
            @endif
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-2 mb-4">{{ $post->title }}</h1>
            <p class="text-white/70 text-sm">
                By {{ $post->author?->name ?? 'Sallaamti' }} — {{ $post->published_at?->format('d M Y') }}
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">Home</a>
                <span>/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a>
                <span>/</span>
                <span class="text-white">{{ $post->title }}</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- POST CONTENT --}}
    {{-- ============================================================ --}}
    <section class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            @if ($post->cover_image)
            <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-80 object-cover rounded-2xl mb-10 shadow-sm" alt="{{ $post->title }}">
            @endif

            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! $post->content !!}
            </div>

            <div class="mt-12 pt-6 border-t border-gray-100">
                <a href="{{ route('blog.index') }}" class="btn-base btn-teal inline-block text-sm px-5 py-2">← Back to Blog</a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- STYLES --}}
    {{-- ============================================================ --}}
    <style>
        :root {
            --teal: #0d6b6b;
            --teal-dark: #095555;
            --gold: #b8962e;
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
    </style>

</x-guest-layout>
