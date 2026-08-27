<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.My Availability') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Weekly Schedule') }}</h3>
                @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                <div class="divide-y">
                    @forelse ($weekly as $slot)
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-sm text-gray-700">{{ __('db.' . $days[$slot->day_of_week]) }}: {{ \Illuminate\Support\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Illuminate\Support\Carbon::parse($slot->end_time)->format('h:i A') }}
                            <span class="text-gray-400 text-xs">({{ $slot->slot_duration_minutes }} {{ __('db.min slots') }})</span>
                        </span>
                        <form method="POST" action="{{ route('counselor.availability.destroy', $slot) }}" onsubmit="return confirm({{ Js::from(__('db.Remove this slot?')) }})">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">{{ __('db.Remove') }}</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 py-2">{{ __('db.No weekly availability set yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Upcoming One-Off Dates') }}</h3>
                <div class="divide-y">
                    @forelse ($overrides as $slot)
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-sm text-gray-700">{{ $slot->specific_date->format('d M Y') }}: {{ \Illuminate\Support\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Illuminate\Support\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                        <form method="POST" action="{{ route('counselor.availability.destroy', $slot) }}" onsubmit="return confirm({{ Js::from(__('db.Remove this slot?')) }})">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">{{ __('db.Remove') }}</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 py-2">{{ __('db.No upcoming one-off dates.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Add Availability') }}</h3>
                <form method="POST" action="{{ route('counselor.availability.store') }}" class="space-y-4" x-data="{ mode: 'weekly' }">
                    @csrf
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-1"><input type="radio" x-model="mode" value="weekly" checked> {{ __('db.Repeats weekly') }}</label>
                        <label class="flex items-center gap-1"><input type="radio" x-model="mode" value="date"> {{ __('db.One-off date') }}</label>
                    </div>

                    <div x-show="mode === 'weekly'">
                        <x-input-label for="day_of_week" :value="__('db.Day of Week')" />
                        <select :name="mode === 'weekly' ? 'day_of_week' : ''" id="day_of_week" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            @foreach ($days as $i => $day)
                            <option value="{{ $i }}">{{ __('db.' . $day) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="mode === 'date'" x-cloak>
                        <x-input-label for="specific_date" :value="__('db.Date')" />
                        <input id="specific_date" :name="mode === 'date' ? 'specific_date' : ''" type="date"
                            class="border-gray-300 rounded-md shadow-sm w-full mt-1" />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="start_time" :value="__('db.Start Time')" />
                            <x-text-input id="start_time" name="start_time" type="time" class="w-full mt-1" required />
                        </div>
                        <div>
                            <x-input-label for="end_time" :value="__('db.End Time')" />
                            <x-text-input id="end_time" name="end_time" type="time" class="w-full mt-1" required />
                        </div>
                        <div>
                            <x-input-label for="slot_duration_minutes" :value="__('db.Slot Length (min)')" />
                            <x-text-input id="slot_duration_minutes" name="slot_duration_minutes" type="number" class="w-full mt-1" value="30" required />
                        </div>
                    </div>

                    <x-primary-button>{{ __('db.Add') }}</x-primary-button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
