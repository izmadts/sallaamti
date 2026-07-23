<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Monthly Fee — {{ $course->title }} ({{ $month }})</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold mb-2">Fee: Rs. {{ number_format($course->monthly_fee) }}</h3>
            <div class="bg-gray-50 border rounded p-4 text-sm mb-6">
                <img src="{{ asset('images/jazzcash.png') }}" alt="Icon Description" class="h-8 w-auto">
                <p class="text-gray-600 mb-0">{{ setting('jazzcash_number', '03XX-XXXXXXX') }}</p>
                <p class="font-semibold text-gray-700 mb-0">{{ setting('jazzcash_account_title', 'Mubashar Irshad') }}</p>
                <!-- <p><strong>EasyPaisa:</strong> {{ setting('easypaisa_number') }}</p> -->
                <img src="{{ asset('images/meezan.png') }}" alt="Icon Description" class="h-16 w-auto">
                <p><strong>Account Title:</strong> {{ setting('bank_account_title') }}</p>
                @if (setting('bank_name'))
                <p class="font-bold mb-0.5" style="color: var(--gold)">🏦 Bank Transfer</p>
                <p class="text-gray-600 text-xs mb-0">Bank: {{ setting('bank_name') }}</p>
                <p class="text-gray-600 text-xs mb-0">Account No: {{ setting('bank_account_number') }}</p>
                <p class="text-gray-600 text-xs mb-0">IBAN: {{ setting('bank_account_iban') }}</p>
                <p class="text-gray-600 text-xs mb-0">Title: {{ setting('site_name', 'Sallaamti') }}</p>
                @endif
            </div>

            @if ($subscription && $subscription->payment_status === 'submitted')
            <div class="p-4 bg-yellow-50 text-yellow-700 rounded text-sm">⏳ Awaiting confirmation.</div>
            @else
            @if ($subscription && $subscription->payment_status === 'rejected')
            <div class="p-4 bg-red-50 text-red-700 rounded text-sm mb-4">❌ {{ $subscription->payment_rejection_reason }}</div>
            @endif
            <form method="POST" action="{{ route('quran-live.subscribe.store', $course) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label value="Payment Method" />
                    <select name="payment_method" class="border-gray-300 rounded-md w-full mt-1" required>
                        <option value="jazzcash">JazzCash</option>
                        <!-- <option value="easypaisa">EasyPaisa</option> -->
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div><x-input-label value="Transaction Reference" /><x-text-input name="payment_reference" class="w-full mt-1" required /></div>
                <div><x-input-label value="Screenshot" /><input type="file" name="payment_screenshot" accept="image/*" class="w-full mt-1" required></div>
                <x-primary-button>Submit Payment</x-primary-button>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>