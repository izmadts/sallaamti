<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">🔖 {{ __('db.Saved') }}</h2>
    </x-slot>

    <div class="py-6" style="background: #fbfaf7; min-height: 60vh">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row gap-6">

                <aside class="lg:w-56 lg:flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 lg:sticky lg:top-4">
                        <a href="{{ route('wall.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[--teal] px-2 py-2">
                            ← {{ __('db.Back to the Wall') }}
                        </a>
                    </div>
                </aside>

                <div class="flex-1 min-w-0 space-y-5">

                    <p class="text-sm text-gray-500">{{ __('db.Posts you\'ve bookmarked from the Sallaamti Wall.') }}</p>

                    <div id="dua-grid" class="space-y-4" data-prune-on-unsave="1">
                        @forelse ($paginated as $item)
                        @continue (!$item)
                        @if ($item instanceof \App\Models\DuaRequest)
                        @include('dua-wall.partials.dua-card', ['dua' => $item])
                        @else
                        @include('dua-wall.partials.post-card', ['post' => $item])
                        @endif
                        @empty
                        <div class="text-center py-16">
                            <div class="text-5xl mb-3 opacity-40">🔖</div>
                            <p class="text-gray-500">{{ __('db.Nothing saved yet — tap Save on any post to keep it here.') }}</p>
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

    @include('dua-wall.partials.wall-scripts', ['feedUrl' => route('wall.saved'), 'paginated' => $paginated])
</x-app-layout>
