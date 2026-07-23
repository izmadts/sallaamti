<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verification Fee Payment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">Fee Amount: Rs. {{ number_format($profile->payment_amount ?? config('services.nikah.verification_fee')) }}</h3>
                <p class="text-sm text-gray-500 mb-4">A verification fee confirms your serious intent and helps us maintain a trustworthy matchmaking community. Please send the amount via JazzCash or EasyPaisa to the number below, then submit your payment details for confirmation.</p>

                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm mb-6">
                    <div class="mb-2">
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
                </div>
                @if ($profile->payment_status === 'submitted')
                <div class="p-4 bg-yellow-50 text-yellow-700 rounded text-sm">
                    ⏳ Your payment proof has been submitted and is awaiting confirmation by our team.
                </div>
                @elseif ($profile->payment_status === 'confirmed')
                <div class="p-4 bg-green-50 text-green-700 rounded text-sm">
                    ✅ Payment confirmed. Your profile will now proceed to CNIC verification.
                </div>
                @else
                @if ($profile->payment_status === 'rejected')
                <div class="p-4 bg-red-50 text-red-700 rounded text-sm mb-4">
                    ❌ Your previous payment proof was rejected. Reason: {{ $profile->payment_rejection_reason }}
                    <br>Please resubmit below.
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Payment Method" />
                        <select name="payment_method" required class="border-gray-300 rounded-md w-full mt-1">
                            <option value="jazzcash">JazzCash</option>
                            <!-- <option value="easypaisa">EasyPaisa</option> -->
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Transaction ID / Reference Number" />
                        <x-text-input name="payment_reference" class="w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label value="Payment Screenshot" />
                        <input type="file" name="payment_screenshot" accept="image/*" class="w-full mt-1" required>
                    </div>
                    <x-primary-button>Submit Payment Proof</x-primary-button>
                </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>