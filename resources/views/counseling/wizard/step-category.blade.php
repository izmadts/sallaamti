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

                <h3 class="font-semibold text-gray-700 mb-1">{{ __('db.What would you like to talk about?') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __("db.This helps us connect you with the right counselor. It's kept private.") }}</p>

                <form method="POST" action="{{ route('counseling.book.step.save', 'category') }}" class="space-y-3">
                    @csrf
                    @php
                    $current = old('category', $data['category'] ?? '');
                    $options = [
                        'marital' => ['💍', 'Marital Concerns'],
                        'parenting' => ['👨‍👩‍👧', 'Parenting'],
                        'financial' => ['💰', 'Financial Stress'],
                        'legal' => ['⚖️', 'Legal Guidance'],
                        'spiritual' => ['🕌', 'Spiritual / Deen'],
                        'other' => ['💬', 'Something Else'],
                    ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($options as $value => [$icon, $label])
                        <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:border-teal-400 {{ $current === $value ? 'border-teal-600 bg-teal-50' : 'border-gray-200' }}">
                            <input type="radio" name="category" value="{{ $value }}" {{ $current === $value ? 'checked' : '' }} class="text-teal-700" required>
                            <span class="text-xl">{{ $icon }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ __('db.' . $label) }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-4">
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
