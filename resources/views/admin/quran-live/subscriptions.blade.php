<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $course->title }} — This Month's Payments</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))<div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>@endif
            @forelse ($subscriptions as $sub)
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $sub->user->name }} ({{ $sub->user->email }})</p>
                        <p class="text-sm text-gray-500">Rs. {{ number_format($sub->amount) }} via {{ $sub->payment_method }} | Ref: {{ $sub->payment_reference }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $sub->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : ($sub->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($sub->payment_status) }}</span>
                </div>
                @if ($sub->payment_screenshot)
                <a href="{{ route('quran-subscription.screenshot', $sub) }}" target="_blank">
                    <img src="{{ route('quran-subscription.screenshot', $sub) }}" class="w-40 rounded border mt-3 hover:opacity-80">
                </a>
                @endif
                @if ($sub->payment_status === 'submitted')
                <div class="mt-4 flex gap-3">
                    <form method="POST" action="{{ route('admin.quran-subscriptions.confirm', $sub) }}">@csrf<button class="bg-green-600 text-white text-sm px-4 py-2 rounded">Confirm</button></form>
                    <form method="POST" action="{{ route('admin.quran-subscriptions.reject', $sub) }}" class="flex gap-2">@csrf<input type="text" name="payment_rejection_reason" placeholder="Reason" class="border-gray-300 rounded text-sm px-2" required><button class="bg-red-600 text-white text-sm px-4 py-2 rounded">Reject</button></form>
                </div>
                @endif
            </div>
            @empty
            <p class="text-gray-500">No payments yet this month.</p>
            @endforelse
            {{ $subscriptions->links() }}
        </div>
    </div>
</x-app-layout>