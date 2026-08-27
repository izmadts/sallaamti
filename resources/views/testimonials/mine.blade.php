<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">⭐ {{ __('db.My Testimonials') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('testimonials.create') }}" class="btn-base bg-teal-700 text-white text-sm px-4 py-2 rounded-md hover:bg-teal-800">+ {{ __('db.Share Your Story') }}</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($testimonials as $testimonial)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex gap-0.5 mb-1">
                            @for ($i = 0; $i < $testimonial->rating; $i++)
                                <span style="color: var(--gold)">★</span>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($testimonial->content), 150) }}</p>
                        @if ($testimonial->status === 'rejected' && $testimonial->rejection_reason)
                        <p class="text-xs text-red-600 mt-1.5">{{ __('db.Reason: :reason', ['reason' => $testimonial->rejection_reason]) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">{{ $testimonial->created_at->format('d M Y') }}</p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $testimonial->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($testimonial->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($testimonial->status) }}
                        </span>
                        <div class="flex gap-3 text-xs">
                            <a href="{{ route('testimonials.edit', $testimonial) }}" class="text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                            <form method="POST" action="{{ route('testimonials.destroy', $testimonial) }}" onsubmit="return confirm({{ Js::from(__('db.Delete this testimonial permanently?')) }})">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">{{ __('db.Delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">{{ __('db.You haven\'t shared a testimonial yet.') }}</p>
                @endforelse
            </div>

            {{ $testimonials->links() }}
        </div>
    </div>
</x-app-layout>
