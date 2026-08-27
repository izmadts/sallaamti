<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">⭐ {{ __('db.Edit Your Story') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                @if ($testimonial->status === 'approved')
                <p class="text-sm text-amber-600 bg-amber-50 rounded-lg p-3 mb-4">
                    {{ __('db.This testimonial is already live. Saving changes will send it back for review before it\'s visible again.') }}
                </p>
                @elseif ($testimonial->status === 'rejected' && $testimonial->rejection_reason)
                <p class="text-sm text-red-600 bg-red-50 rounded-lg p-3 mb-4">
                    {{ __('db.Not approved: :reason', ['reason' => $testimonial->rejection_reason]) }}
                </p>
                @endif

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" :value="__('db.Your Name')" />
                        <x-text-input id="name" name="name" class="w-full mt-1" :value="old('name', $testimonial->name)" required maxlength="100" />
                    </div>
                    <div>
                        <x-input-label for="location" :value="__('db.City / Location (optional)')" />
                        <x-text-input id="location" name="location" class="w-full mt-1" :value="old('location', $testimonial->location)" maxlength="100" />
                    </div>

                    <div x-data="{ rating: {{ old('rating', $testimonial->rating) }} }">
                        <x-input-label :value="__('db.Your Rating')" />
                        <div class="flex gap-1 mt-1">
                            <template x-for="star in [1,2,3,4,5]" :key="star">
                                <button type="button" @click="rating = star" class="text-3xl leading-none transition-transform hover:scale-110"
                                    :style="star <= rating ? 'color: var(--gold)' : 'color: #e5e7eb'">★</button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>

                    <div>
                        <x-input-label for="content" :value="__('db.Your Story')" />
                        <input id="trix-content" type="hidden" name="content" value="{{ old('content', $testimonial->content) }}">
                        <trix-editor input="trix-content"></trix-editor>
                    </div>
                    <div>
                        <x-input-label for="photo" :value="__('db.Your Photo')" />
                        @if ($testimonial->photo)
                        <div class="mt-1 mb-2">
                            <img src="{{ Storage::url($testimonial->photo) }}" class="w-16 h-16 rounded-full object-cover">
                        </div>
                        @endif
                        <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1">
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>{{ __('db.Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
