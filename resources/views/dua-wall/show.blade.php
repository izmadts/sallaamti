<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'guest-layout'" :title="$title" :description="$description" :image="$image">
    @if (auth()->check())
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">🤲 {{ __('db.Sallaamti Wall') }}</h2>
    </x-slot>
    @endif

    <div class="py-6" style="background: #fbfaf7; min-height: 60vh">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <a href="{{ route('wall.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                ← {{ __('db.Back to the Wall') }}
            </a>

            @include($partial, $partialData)
        </div>
    </div>

    @include('dua-wall.partials.wall-scripts', ['feedUrl' => route('wall.index'), 'paginated' => $paginated])
</x-dynamic-component>
