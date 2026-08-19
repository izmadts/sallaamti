<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Review Your Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" current="slot" />

                <p class="text-sm text-gray-500 mb-6">{{ __('db.Please review your session details before submitting.') }}</p>

                <div class="space-y-4">
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400">{{ __('db.Concern') }}</p>
                            <p class="text-sm text-gray-800 font-medium">{{ ucfirst($data['category'] ?? '') }} — {{ $data['subject'] ?? '' }}</p>
                        </div>
                        <a href="{{ route('counseling.book.step', 'category') }}" class="text-xs text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                    </div>

                    <div class="border rounded-lg p-4">
                        <p class="text-xs text-gray-400 mb-1">{{ __('db.Description') }}</p>
                        <p class="text-sm text-gray-700">{{ $data['description'] ?? '' }}</p>
                        @if (!empty($data['is_anonymous']))
                        <p class="text-xs text-gray-400 mt-2">🔒 {{ __('db.Anonymous to counselor') }}</p>
                        @endif
                        @if (!empty($data['is_urgent']))
                        <p class="text-xs text-red-600 font-semibold mt-2">🚨 {{ __('db.Flagged as urgent / a safety concern') }}</p>
                        @endif
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400">{{ __('db.Contact Method') }}</p>
                            <p class="text-sm text-gray-800 font-medium">{{ ucfirst(str_replace('_', ' ', $data['contact_method'] ?? '')) }}</p>
                        </div>
                        <a href="{{ route('counseling.book.step', 'contact') }}" class="text-xs text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400">{{ __('db.Counselor') }}</p>
                            <p class="text-sm text-gray-800 font-medium">{{ $counselor?->name ?? __('db.To be assigned') }}</p>
                        </div>
                        <a href="{{ route('counseling.book.step', 'counselor') }}" class="text-xs text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400">{{ __('db.Scheduled Time') }}</p>
                            <p class="text-sm text-gray-800 font-medium">{{ \Illuminate\Support\Carbon::parse($data['scheduled_at'] ?? now())->format('l, d M Y — h:i A') }}</p>
                        </div>
                        <a href="{{ route('counseling.book.step', 'slot') }}" class="text-xs text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mt-4">{{ __('db.This sends your request — nothing is booked yet until a counselor confirms the time.') }}</p>

                <form method="POST" action="{{ route('counseling.book.finalize') }}" class="mt-2 flex justify-between">
                    @csrf
                    <a href="{{ route('counseling.book.step', 'slot') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                    <x-primary-button>{{ __('db.Confirm & Send Request') }} →</x-primary-button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
