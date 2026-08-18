@php
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

            <div class="mt-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                    <x-reaction-picker :model="$dua" :react-url="route('wall.react', $dua)" />
                    <button type="button" data-comments-toggle data-thread-id="dua-{{ $dua->id }}" data-comments-url="{{ route('wall.comments', $dua) }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300">
                        💬 <span data-comments-count>{{ $dua->comments_count ?? 0 }}</span>
                    </button>
                </div>
                <x-save-button :model="$dua" :save-url="route('wall.save', $dua)" />
            </div>
            <div data-comments-container data-thread-id="dua-{{ $dua->id }}" class="hidden mt-3"></div>
        </div>
    </div>
</div>
