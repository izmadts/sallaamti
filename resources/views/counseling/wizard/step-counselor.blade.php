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

                <h3 class="font-semibold text-gray-700 mb-1">{{ __('db.Choose a counselor') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __("db.Not sure who to pick? Let us assign the next available counselor for you.") }}</p>

                <form method="POST" action="{{ route('counseling.book.step.save', 'counselor') }}" class="space-y-3">
                    @csrf
                    @php $current = old('counselor_choice', $data['counselor_choice'] ?? 'auto'); @endphp

                    <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:border-teal-400 {{ $current === 'auto' ? 'border-teal-600 bg-teal-50' : 'border-gray-200' }}">
                        <input type="radio" name="counselor_choice" value="auto" {{ $current === 'auto' ? 'checked' : '' }} class="text-teal-700">
                        <span class="text-xl">✨</span>
                        <span class="text-sm font-medium text-gray-700">{{ __('db.Auto-assign me the next available counselor') }}</span>
                    </label>

                    @foreach ($counselors as $counselor)
                    <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:border-teal-400 {{ (string) $current === (string) $counselor->id ? 'border-teal-600 bg-teal-50' : 'border-gray-200' }}">
                        <input type="radio" name="counselor_choice" value="{{ $counselor->id }}" {{ (string) $current === (string) $counselor->id ? 'checked' : '' }} class="text-teal-700">
                        <img src="{{ $counselor->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium text-gray-700">{{ $counselor->name }}</span>
                    </label>
                    @endforeach

                    @if ($counselors->isEmpty())
                    <p class="text-sm text-gray-400 italic">{{ __('db.No counselors listed by name yet — auto-assign will still work once one is available.') }}</p>
                    @endif

                    <div class="flex justify-between pt-4">
                        <a href="{{ route('counseling.book.step', 'contact') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
