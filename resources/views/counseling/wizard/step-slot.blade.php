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

                <div class="flex items-center justify-between mb-4 bg-gray-50 rounded-lg p-2 gap-2">
                    <a href="{{ route('counseling.book.step', ['step' => 'slot', 'date' => $date->copy()->subDay()->toDateString()]) }}"
                        class="px-3 py-1 text-sm text-gray-600 hover:text-teal-700 whitespace-nowrap {{ $date->isToday() ? 'invisible' : '' }}">← {{ __('db.Prev day') }}</a>
                    <span class="font-semibold text-gray-700 text-sm sm:text-base">{{ $date->format('l, d M Y') }}</span>
                    <a href="{{ route('counseling.book.step', ['step' => 'slot', 'date' => $date->copy()->addDay()->toDateString()]) }}"
                        class="px-3 py-1 text-sm text-gray-600 hover:text-teal-700 whitespace-nowrap">{{ __('db.Next day') }} →</a>
                </div>

                <input type="date" value="{{ $date->toDateString() }}" min="{{ \Illuminate\Support\Carbon::today()->toDateString() }}"
                    onchange="window.location.href = '{{ route('counseling.book.step', 'slot') }}?date=' + this.value"
                    class="w-full mb-4 border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg text-sm"
                    title="{{ __('db.Jump straight to a specific date') }}">

                @php $openSlots = collect($slots)->reject(fn ($slot) => $slot['booked']); @endphp

                <form method="POST" action="{{ route('counseling.book.step.save', 'slot') }}" class="space-y-4">
                    @csrf

                    @if (empty($slots))
                    <p class="text-sm text-gray-400 italic p-4 text-center border rounded-lg">{{ __('db.No available slots on this day — try another date, or request a session below without picking an exact slot.') }}</p>
                    @else
                    @if ($openSlots->isEmpty())
                    <p class="text-sm text-gray-400 italic p-4 text-center border rounded-lg">{{ __('db.This counselor is fully booked on this day — try another date, or request a session below without picking an exact slot.') }}</p>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($slots as $slot)
                        @if ($slot['booked'])
                        <span class="border border-gray-200 bg-gray-50 rounded-lg p-3 text-center cursor-not-allowed" title="{{ __('db.Already booked') }}">
                            <span class="block text-sm font-medium text-gray-400 line-through">{{ $slot['datetime']->format('h:i A') }}</span>
                            <span class="block text-[11px] text-gray-400 mt-0.5">{{ __('db.Booked') }}</span>
                        </span>
                        @else
                        @php $value = $slot['counselor_id'] . '|' . $slot['datetime']->toDateTimeString(); @endphp
                        <label class="border rounded-lg p-3 text-center cursor-pointer hover:border-teal-400 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50">
                            <input type="radio" name="slot" value="{{ $value }}" class="sr-only" required>
                            <span class="block text-sm font-medium text-gray-700">{{ $slot['datetime']->format('h:i A') }}</span>
                        </label>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('counseling.book.step', 'counselor') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button :disabled="$openSlots->isEmpty()">{{ __('db.Review & Submit') }} →</x-primary-button>
                    </div>
                </form>

                {{-- Fallback: no counselor has open availability (or none is
                     registered yet) — let the member request anyway with a
                     preferred time; admin assigns a counselor afterward. --}}
                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-700 mb-1">{{ __("db.Can't find a time that works?") }}</h4>
                    <p class="text-sm text-gray-500 mb-3">{{ __('db.Tell us your preferred date and time — we\'ll assign a counselor and confirm with you.') }}</p>
                    <form method="POST" action="{{ route('counseling.book.step.save', 'slot') }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="datetime-local" name="preferred_at" required
                            min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                            class="flex-1 border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg text-sm">
                        <button class="border border-teal-700 text-teal-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-teal-50 transition whitespace-nowrap">
                            {{ __('db.Request This Time') }} →
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
