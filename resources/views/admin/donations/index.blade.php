<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Donations</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">


            <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                <p class="text-sm text-gray-600">Total Confirmed Donations</p>
                <p class="text-xl font-semibold text-green-600">Rs. {{ number_format($totalConfirmed) }}</p>
            </div>

            <form method="GET" class="flex gap-2">
                <select name="status" class="border-gray-300 rounded text-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($donations as $donation)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <p class="font-medium">{{ $donation->donor_name }} {{ $donation->user_id ? '' : '(Guest)' }}</p>
                        <p class="text-sm text-gray-500">{{ $donation->email }} {{ $donation->phone }}</p>
                        <p class="text-sm text-gray-500">Rs. {{ number_format($donation->amount) }} — {{ $donation->purpose }}</p>
                        <p class="text-sm text-gray-500">Ref: {{ $donation->payment_reference }} via {{ $donation->payment_method }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $donation->donation_number }} | {{ $donation->created_at->format('d M Y, h:i A') }}</p>

                        @if ($donation->payment_screenshot)
                        <a href="{{ route('admin.admin.donation.screenshot', $donation) }}" target="_blank" class="text-xs text-blue-600 underline">View Screenshot</a>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $donation->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                   ($donation->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($donation->payment_status) }}
                        </span>

                        @if ($donation->payment_status === 'submitted')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.donations.confirm', $donation) }}">
                                @csrf
                                <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('admin.donations.reject', $donation) }}" class="flex gap-1">
                                @csrf
                                <input type="text" name="payment_rejection_reason" placeholder="Reason" class="border-gray-300 rounded text-xs px-2 w-28" required>
                                <button class="bg-red-600 text-white text-sm px-3 py-1.5 rounded">Reject</button>
                            </form>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('admin.donations.destroy', $donation) }}" onsubmit="return confirm('Permanently delete this donation record?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600 hover:underline">Delete record</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No donations yet.</p>
                @endforelse
            </div>
            {{ $donations->links() }}
        </div>
    </div>
</x-admin-layout>