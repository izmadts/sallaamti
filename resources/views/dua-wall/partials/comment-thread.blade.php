@php
    $storeUrl = $commentable instanceof \App\Models\DuaRequest
        ? route('wall.comments.store', $commentable)
        : route('wall.post.comments.store', $commentable);
@endphp

<div class="border-t border-gray-100 pt-3 space-y-3" data-comment-thread>
    @auth
    <form method="POST" action="{{ $storeUrl }}" class="flex items-start gap-2" data-comment-form data-parent-id="">
        @csrf
        <img src="{{ auth()->user()->avatarUrl() }}" loading="lazy" class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5">
        <div class="flex-1">
            <textarea name="body" rows="1" maxlength="1000" required placeholder="{{ __('db.Write a comment…') }}"
                class="w-full border-gray-300 rounded-lg text-sm focus:border-[--teal] focus:ring-[--teal]"></textarea>
            <button class="mt-1.5 text-white text-xs font-semibold px-3 py-1 rounded-lg" style="background: var(--teal)">{{ __('db.Post') }}</button>
        </div>
    </form>
    @endauth

    <div class="space-y-3">
        @forelse ($comments as $comment)
        @include('dua-wall.partials.comment-item', ['comment' => $comment, 'storeUrl' => $storeUrl])
        @empty
        <p class="text-xs text-gray-400">{{ __('db.No comments yet — be the first to reply.') }}</p>
        @endforelse
    </div>
</div>
