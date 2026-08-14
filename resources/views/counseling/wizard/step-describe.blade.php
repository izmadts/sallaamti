<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Book a Family Counseling Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <h3 class="font-semibold text-gray-700 mb-1">{{ __('db.Tell us a bit more') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.A short summary helps your counselor prepare — you can share more detail during the session.') }}</p>

                <form method="POST" action="{{ route('counseling.book.step.save', 'describe') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="subject" :value="__('db.Subject')" />
                        <x-text-input id="subject" name="subject" type="text" class="w-full mt-1" :value="old('subject', $data['subject'] ?? '')" required />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('db.Briefly describe your situation')" />
                        <textarea id="description" name="description" rows="5" required minlength="20"
                            class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('description', $data['description'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">{{ __('db.Minimum 20 characters.') }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous', $data['is_anonymous'] ?? false) ? 'checked' : '' }} class="rounded">
                        <x-input-label for="is_anonymous" :value="__('db.Keep my identity anonymous to the counselor')" />
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('counseling.book.step', 'category') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
