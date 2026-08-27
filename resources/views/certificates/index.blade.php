<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.My Certificates') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($certificates as $cert)
            <div class="bg-white rounded-lg shadow-sm p-5 flex justify-between items-center">
                <div>
                    <p class="font-medium">{{ $cert->type === 'course' ? $cert->course?->title : $cert->title }}</p>
                    <p class="text-sm text-gray-500">{{ __('db.Issued :date — :number', ['date' => $cert->issued_at->format('M j, Y'), 'number' => $cert->certificate_number]) }}</p>
                </div>
                <a href="{{ route('certificate.download', $cert) }}" class="bg-gray-800 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">{{ __('db.Download PDF') }}</a>
            </div>
            @empty
            <p class="text-gray-500">{{ __('db.No certificates earned yet — complete a course to earn one!') }}</p>
            @endforelse
        </div>
    </div>
</x-app-layout>