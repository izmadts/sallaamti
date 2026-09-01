<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.My Certificates') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($certificates as $cert)
            <div class="bg-white rounded-lg shadow-sm p-5 flex justify-between items-center gap-4">
                <div>
                    <p class="font-medium">{{ $cert->type === 'course' ? $cert->course?->title : $cert->title }}</p>
                    @if ($cert->isApproved())
                    <p class="text-sm text-gray-500">{{ __('db.Issued :date — :number', ['date' => $cert->issued_at->format('M j, Y'), 'number' => $cert->certificate_number]) }}</p>
                    @elseif ($cert->isPending())
                    <p class="text-sm text-amber-600">⏳ {{ __('db.Awaiting admin review') }}</p>
                    @else
                    <p class="text-sm text-red-600">❌ {{ $cert->rejection_reason ?: __('db.Not approved') }}</p>
                    @endif
                </div>
                @if ($cert->isApproved())
                <a href="{{ route('certificate.download', $cert) }}" class="shrink-0 bg-gray-800 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">{{ __('db.Download PDF') }}</a>
                @endif
            </div>
            @empty
            <p class="text-gray-500">{{ __('db.No certificates earned yet — complete a course to earn one!') }}</p>
            @endforelse
        </div>
    </div>
</x-app-layout>