<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">⭐ Share Your Story</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <p class="text-sm text-gray-500 mb-6">
                    Tell other visitors what your experience with Sallaamti has been like. It'll appear on our
                    Testimonials page once our team reviews it, and you'll be notified either way.
                </p>

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('testimonials.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Your Name" />
                        <x-text-input id="name" name="name" class="w-full mt-1" :value="old('name', auth()->user()->name)" required maxlength="100" />
                    </div>
                    <div>
                        <x-input-label for="location" value="City / Location (optional)" />
                        <x-text-input id="location" name="location" class="w-full mt-1" :value="old('location')" maxlength="100" placeholder="e.g. Karachi, Pakistan" />
                    </div>

                    <div x-data="{ rating: {{ old('rating', 5) }} }">
                        <x-input-label value="Your Rating" />
                        <div class="flex gap-1 mt-1">
                            <template x-for="star in [1,2,3,4,5]" :key="star">
                                <button type="button" @click="rating = star" class="text-3xl leading-none transition-transform hover:scale-110"
                                    :style="star <= rating ? 'color: var(--gold)' : 'color: #e5e7eb'">★</button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>

                    <div>
                        <x-input-label for="content" value="Your Story" />
                        <textarea id="content" name="content" rows="6" required maxlength="2000" class="border-gray-300 rounded-md w-full mt-1"
                            placeholder="What did Sallaamti help you with? What would you tell someone considering us?">{{ old('content') }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="photo" value="Your Photo (optional)" />
                        <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1">
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Submit for Review</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
