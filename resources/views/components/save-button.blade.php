{{-- Bookmark toggle for anything saveable (DuaRequest, CommunityPost).
     Guests get a plain link to /login, same reasoning as reaction-picker:
     a real page navigation so redirect()->intended() sends them back here
     after signing in. --}}
@props(['model', 'saveUrl'])

@php
$isSaved = auth()->check() ? $model->savedBy(auth()->user()) : false;
@endphp

@auth
<button type="button" data-save data-url="{{ $saveUrl }}"
    class="save-btn inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-full border transition {{ $isSaved ? 'bg-amber-50 border-amber-300 text-amber-700' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
    <span data-save-icon>{{ $isSaved ? '🔖' : '📑' }}</span>
    <span class="hidden sm:inline" data-save-label>{{ $isSaved ? __('db.Saved') : __('db.Save') }}</span>
</button>
@else
<a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300">
    📑 {{ __('db.Save') }}
</a>
@endauth
