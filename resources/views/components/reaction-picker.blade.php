{{-- Shared reaction picker for anything reactable (DuaRequest, CommunityPost).
     A user has at most one active reaction per post — tapping a different
     type swaps it, tapping the active one removes it. Guests get a plain
     link to /login (not an interactive button) so clicking it is a real
     page navigation and Laravel's redirect()->intended() sends them back
     here automatically after signing in. --}}
@props(['model', 'reactUrl'])

@php
$activeType = auth()->check() ? $model->reactionTypeBy(auth()->user()) : null;
$total = $model->reactions->count();
@endphp

<div class="flex items-center gap-1.5 flex-wrap" data-reaction-group>
    @auth
    @foreach (\App\Models\Reaction::TYPES as $type => [$emoji, $label])
    <button type="button" data-react data-url="{{ $reactUrl }}" data-type="{{ $type }}"
        class="reaction-btn inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-full border transition {{ $activeType === $type ? 'bg-teal-50 border-teal-300 text-[--teal]' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
        <span>{{ $emoji }}</span>
        <span class="hidden sm:inline">{{ __("db.$label") }}</span>
    </button>
    @endforeach
    <span class="reaction-total text-xs text-gray-400 {{ $total > 0 ? '' : 'hidden' }}">{{ $total > 0 ? $total : '' }}</span>
    @else
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300">
        🤲 {{ __('db.React') }}
    </a>
    <span class="reaction-total text-xs text-gray-400 {{ $total > 0 ? '' : 'hidden' }}">{{ $total > 0 ? $total : '' }}</span>
    @endauth
</div>
