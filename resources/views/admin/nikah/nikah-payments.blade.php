<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Payment Confirmations</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            @forelse ($profiles as $profile)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">{{ $profile->user->name }} ({{ $profile->user->email }})</h3>
                        <p class="text-sm text-gray-500">Amount: Rs. {{ number_format($profile->payment_amount) }} via {{ ucfirst(str_replace('_', ' ', $profile->payment_method)) }}</p>
                        <p class="text-sm text-gray-500">Reference: {{ $profile->payment_reference }}</p>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Submitted</span>
                </div>

                @if ($profile->payment_screenshot)
                <div class="mt-3">
                    <a href="{{ route('nikah.file', [$profile, 'payment_screenshot']) }}" target="_blank">
                        <img src="{{ route('nikah.file', [$profile, 'payment_screenshot']) }}" class="w-48 rounded border hover:opacity-80">
                    </a>
                </div>
                @endif

                <div class="mt-4 flex gap-3">
                    <form method="POST" action="{{ route('admin.nikah.payments.confirm', $profile) }}">
                        @csrf
                        <button class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">Confirm Payment</button>
                    </form>
                    <form method="POST" action="{{ route('admin.nikah.payments.reject', $profile) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="payment_rejection_reason" placeholder="Reason" class="border-gray-300 rounded text-sm px-2" required>
                        <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Reject</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No pending payment confirmations.</p>
            @endforelse

            {{ $profiles->links() }}
        </div>
    </div>
</x-app-layout>