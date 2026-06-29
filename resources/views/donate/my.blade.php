<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Donations</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">
            @forelse ($donations as $donation)
            <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium">Rs. {{ number_format($donation->amount) }} — {{ $donation->purpose }}</p>
                    <p class="text-xs text-gray-400">{{ $donation->donation_number }} | {{ $donation->created_at->format('d M Y') }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $donation->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : ($donation->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($donation->payment_status) }}
                </span>
            </div>
            @empty
            <p class="text-gray-500">You haven't made any donations yet. <a href="{{ route('donate.create') }}" class="text-pink-600 underline">Donate now</a></p>
            @endforelse
        </div>
    </div>
</x-app-layout>