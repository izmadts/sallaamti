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

                <h3 class="font-semibold text-gray-700 mb-1">{{ __('db.Pick a time') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.All times shown are your local time.') }}</p>

                <div class="flex items-center justify-between mb-4 bg-gray-50 rounded-lg p-2">
                    <a href="{{ route('counseling.book.step', ['step' => 'slot', 'date' => $date->copy()->subDay()->toDateString()]) }}"
                        class="px-3 py-1 text-sm text-gray-600 hover:text-teal-700 {{ $date->isToday() ? 'invisible' : '' }}">← {{ __('db.Prev day') }}</a>
                    <span class="font-semibold text-gray-700">{{ $date->format('l, d M Y') }}</span>
                    <a href="{{ route('counseling.book.step', ['step' => 'slot', 'date' => $date->copy()->addDay()->toDateString()]) }}"
                        class="px-3 py-1 text-sm text-gray-600 hover:text-teal-700">{{ __('db.Next day') }} →</a>
                </div>

                <form method="POST" action="{{ route('counseling.book.step.save', 'slot') }}" class="space-y-4">
                    @csrf

                    @if (empty($slots))
                    <p class="text-sm text-gray-400 italic p-4 text-center border rounded-lg">{{ __('db.No available slots on this day — try another date.') }}</p>
                    @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($slots as $slot)
                        @php $value = $slot['counselor_id'] . '|' . $slot['datetime']->toDateTimeString(); @endphp
                        <label class="border rounded-lg p-3 text-center cursor-pointer hover:border-teal-400 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50">
                            <input type="radio" name="slot" value="{{ $value }}" class="sr-only" required>
                            <span class="block text-sm font-medium text-gray-700">{{ $slot['datetime']->format('h:i A') }}</span>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('counseling.book.step', 'counselor') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button :disabled="empty($slots)">{{ __('db.Review & Submit') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
