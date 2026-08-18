@php
    $canDelete = auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole('admin'));
@endphp

<div class="flex items-start gap-2" data-comment-id="{{ $comment->id }}">
    <img src="{{ $comment->user->avatarUrl() }}" loading="lazy" class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5">
    <div class="flex-1 min-w-0">
        <div class="bg-gray-50 rounded-xl px-3 py-2">
            <p class="text-xs font-semibold text-gray-800">{{ $comment->user->name }}</p>
            <p class="text-sm text-gray-700 whitespace-pre-line break-words">{{ $comment->body }}</p>
        </div>
        <div class="flex items-center gap-3 mt-1 px-1">
            <span class="text-[11px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
            @auth
            <button type="button" data-reply-toggle class="text-[11px] font-semibold text-gray-500 hover:text-[--teal]">{{ __('db.Reply') }}</button>
            @endauth
            @if ($canDelete)
            <button type="button" data-comment-delete data-url="{{ route('comments.destroy', $comment) }}" class="text-[11px] font-semibold text-red-500 hover:underline">{{ __('db.Delete') }}</button>
            @endif
        </div>

        @auth
        <form method="POST" action="{{ $storeUrl }}" class="hidden mt-2 flex items-start gap-2" data-comment-form data-parent-id="{{ $comment->id }}">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <img src="{{ auth()->user()->avatarUrl() }}" loading="lazy" class="w-6 h-6 rounded-full object-cover flex-shrink-0 mt-0.5">
            <div class="flex-1">
                <textarea name="body" rows="1" maxlength="1000" required placeholder="{{ __('db.Write a reply…') }}"
                    class="w-full border-gray-300 rounded-lg text-xs focus:border-[--teal] focus:ring-[--teal]"></textarea>
                <button class="mt-1.5 text-white text-xs font-semibold px-3 py-1 rounded-lg" style="background: var(--teal)">{{ __('db.Reply') }}</button>
            </div>
        </form>
        @endauth

        @if ($comment->replies->isNotEmpty())
        <div class="space-y-2 mt-2 ps-3 border-s-2 border-gray-100">
            @foreach ($comment->replies as $reply)
            @php $canDeleteReply = auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->hasRole('admin')); @endphp
            <div class="flex items-start gap-2" data-comment-id="{{ $reply->id }}">
                <img src="{{ $reply->user->avatarUrl() }}" loading="lazy" class="w-6 h-6 rounded-full object-cover flex-shrink-0 mt-0.5">
                <div class="flex-1 min-w-0">
                    <div class="bg-gray-50 rounded-xl px-3 py-2">
                        <p class="text-xs font-semibold text-gray-800">{{ $reply->user->name }}</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line break-words">{{ $reply->body }}</p>
                    </div>
                    <div class="flex items-center gap-3 mt-1 px-1">
                        <span class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                        @if ($canDeleteReply)
                        <button type="button" data-comment-delete data-url="{{ route('comments.destroy', $reply) }}" class="text-[11px] font-semibold text-red-500 hover:underline">{{ __('db.Delete') }}</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
