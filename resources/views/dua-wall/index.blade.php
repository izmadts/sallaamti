<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'guest-layout'">
    @if (auth()->check())
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">🤲 {{ __('db.Sallaamti Wall') }}</h2>
    </x-slot>
    @endif

    {{-- Hero strip — same visual language as Check Profiles --}}
    <div class="text-white" style="background: linear-gradient(120deg, var(--teal-dark, #0d4f4f), var(--teal, #0d6b6b) 60%, #b8962e)">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8 text-center">
            <div class="text-4xl">🤲</div>
            <h1 class="text-xl font-bold mt-2">{{ __('db.Sallaamti Wall') }}</h1>
            <p class="text-teal-50 text-sm mt-1 opacity-90">{{ __('db.A place for the community to share duas, activities, events and sermons.') }}</p>
        </div>
    </div>

    <div class="py-6" style="background: #fbfaf7; min-height: 60vh">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- Tag filter sidebar — horizontal scrollable chips on mobile,
                     a sticky vertical list on desktop. --}}
                <aside class="lg:w-56 lg:flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 lg:sticky lg:top-4">
                        <p class="hidden lg:block text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1 pb-2">{{ __('db.Filter') }}</p>
                        <div class="flex lg:flex-col gap-2 lg:gap-1 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0">
                            <a href="{{ route('wall.index') }}"
                                class="flex-shrink-0 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition {{ $activeTag === 'all' ? 'bg-teal-50 text-[--teal]' : 'text-gray-600 hover:bg-gray-50' }}">
                                🌐 {{ __('db.All') }}
                            </a>
                            <a href="{{ route('wall.index', ['tag' => 'dua']) }}"
                                class="flex-shrink-0 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition {{ $activeTag === 'dua' ? 'bg-teal-50 text-[--teal]' : 'text-gray-600 hover:bg-gray-50' }}">
                                🤲 {{ __('db.Duas') }}
                            </a>
                            @foreach ($tags as $tag)
                            <a href="{{ route('wall.index', ['tag' => $tag]) }}"
                                class="flex-shrink-0 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition {{ $activeTag === $tag ? 'bg-teal-50 text-[--teal]' : 'text-gray-600 hover:bg-gray-50' }}">
                                #{{ $tag }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

                {{-- Main feed --}}
                <div class="flex-1 min-w-0 space-y-5">

                    <x-module-nav module="wall" layout="bar" />

                    @auth
                    {{-- Post a dua --}}
                    <form method="POST" action="{{ route('wall.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                        @csrf
                        <label class="text-sm font-semibold text-gray-700">{{ __('db.Share a dua request') }}</label>
                        <textarea name="body" rows="3" maxlength="1000" required
                            placeholder="{{ __('db.What would you like the community to make dua for?') }}"
                            class="w-full mt-1.5 border-gray-300 rounded-lg text-sm focus:border-[--teal] focus:ring-[--teal]">{{ old('body') }}</textarea>
                        @error('body')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex items-center justify-between">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500">
                                <input type="checkbox" name="is_anonymous" value="1" class="rounded text-[--teal] focus:ring-[--teal]">
                                {{ __('db.Post anonymously') }}
                            </label>
                            <button class="text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm hover:brightness-110 transition" style="background: var(--teal)">
                                {{ __('db.Share') }}
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">{{ __('db.Your dua is reviewed by our team before it appears on the wall.') }}</p>
                    </form>
                    @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-sm text-gray-600">{{ __('db.Join Sallaamti to share your own dua and react to posts.') }}</p>
                        <a href="{{ route('register') }}" class="inline-block mt-2 text-white text-sm font-semibold px-4 py-2 rounded-lg" style="background: var(--teal)">
                            {{ __('db.Join Free') }}
                        </a>
                    </div>
                    @endauth

                    <div id="dua-grid" class="space-y-4">
                        @forelse ($paginated as $item)
                        @if ($item instanceof \App\Models\DuaRequest)
                        @include('dua-wall.partials.dua-card', ['dua' => $item])
                        @else
                        @include('dua-wall.partials.post-card', ['post' => $item])
                        @endif
                        @empty
                        <div class="text-center py-16">
                            <div class="text-5xl mb-3 opacity-40">🤲</div>
                            <p class="text-gray-500">{{ __('db.Nothing here yet — be the first to share something.') }}</p>
                        </div>
                        @endforelse
                    </div>

                    <div id="dua-scroll-sentinel" class="py-8 text-center">
                        <div id="dua-scroll-loading" class="hidden items-center justify-center gap-2 text-sm text-gray-400">
                            <svg class="animate-spin h-4 w-4" style="color: var(--teal)" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ __('db.Loading more…') }}
                        </div>
                        <button id="dua-scroll-load-more" type="button" class="{{ $paginated->hasMorePages() ? '' : 'hidden' }} text-sm font-semibold px-6 py-2.5 rounded-lg border-2 hover:bg-teal-50 transition" style="color: var(--teal); border-color: var(--teal)">
                            {{ __('db.Load More') }} ↓
                        </button>
                        <p id="dua-scroll-end" class="hidden text-sm text-gray-400">{{ __("db.You've reached the end.") }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const grid = document.getElementById('dua-grid');
            const sentinel = document.getElementById('dua-scroll-sentinel');
            const loadingEl = document.getElementById('dua-scroll-loading');
            const endEl = document.getElementById('dua-scroll-end');
            const loadMoreBtn = document.getElementById('dua-scroll-load-more');

            let page = 1;
            let hasMore = {{ $paginated->hasMorePages() ? 'true' : 'false' }};
            let loading = false;

            async function loadNextPage() {
                if (loading || !hasMore) return;
                loading = true;
                loadMoreBtn.classList.add('hidden');
                loadingEl.classList.remove('hidden');
                loadingEl.classList.add('flex');

                page += 1;
                const params = new URLSearchParams(window.location.search);
                params.set('page', page);

                try {
                    const res = await fetch('{{ route('wall.index') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    if (data.html && data.html.trim()) {
                        grid.insertAdjacentHTML('beforeend', data.html);
                    }
                    hasMore = data.has_more;
                } catch (e) {
                    hasMore = false;
                }

                loading = false;
                loadingEl.classList.add('hidden');
                loadingEl.classList.remove('flex');
                if (hasMore) {
                    loadMoreBtn.classList.remove('hidden');
                } else {
                    endEl.classList.remove('hidden');
                    if (observer) observer.disconnect();
                }
            }

            let observer = null;
            if (hasMore) {
                loadMoreBtn.addEventListener('click', loadNextPage);
                observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) loadNextPage();
                }, { rootMargin: '400px' });
                observer.observe(sentinel);
            } else {
                endEl.classList.remove('hidden');
            }

            // Reactions — delegated so it keeps working on cards appended by infinite scroll.
            // A user has one active reaction per post: tapping their current type removes it,
            // tapping a different type swaps it.
            document.addEventListener('click', async function (event) {
                const btn = event.target.closest('[data-react]');
                if (!btn) return;
                event.preventDefault();

                const group = btn.closest('[data-reaction-group]');

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ type: btn.dataset.type }),
                    });
                    const data = await res.json();

                    group.querySelectorAll('[data-react]').forEach((b) => {
                        const active = data.reacted && b.dataset.type === data.type;
                        b.classList.toggle('bg-teal-50', active);
                        b.classList.toggle('border-teal-300', active);
                        b.classList.toggle('text-[--teal]', active);
                        b.classList.toggle('border-gray-200', !active);
                        b.classList.toggle('text-gray-500', !active);
                    });

                    const total = group.querySelector('.reaction-total');
                    if (total) {
                        total.textContent = data.count > 0 ? data.count : '';
                        total.classList.toggle('hidden', data.count === 0);
                    }
                } catch (e) {
                    // silently ignore — worst case the tap just didn't register
                }
            });
        })();
    </script>
</x-dynamic-component>
