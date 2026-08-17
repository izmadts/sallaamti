@php
    $reacted = auth()->check() && $dua->reactedBy(auth()->user());
    $count = $dua->reactions->count();
    $displayName = $dua->is_anonymous ? __('db.A Sallaamti member') : $dua->user->name;
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0" style="background: #f0fdfa">🤲</div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-sm text-gray-800">{{ $displayName }}</p>
                <span class="text-xs text-gray-400">{{ $dua->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-gray-700 text-sm mt-1.5 whitespace-pre-line break-words">{{ $dua->body }}</p>

            <div class="mt-3 flex items-center gap-3">
                @auth
                <button
                    data-dua-react
                    data-dua-id="{{ $dua->id }}"
                    data-url="{{ route('wall.react', $dua) }}"
                    class="dua-react-btn inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border transition {{ $reacted ? 'bg-teal-50 border-teal-300 text-[--teal]' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}"
                >
                    <span class="dua-react-icon">🤲</span>
                    <span class="dua-react-label">{{ $reacted ? __('db.Ameen') : __('db.Say Ameen') }}</span>
                    <span class="dua-react-count">{{ $count > 0 ? $count : '' }}</span>
                </button>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300">
                    🤲 {{ __('db.Say Ameen') }}
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>
