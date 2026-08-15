<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verification Fee Payment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <div class="rounded-xl border-2 p-5" style="border-color: var(--gold); background: linear-gradient(135deg, #fffbeb 0%, #f0fdfa 100%)">
                <div class="flex items-start gap-3">
                    <span class="text-2xl shrink-0">🛡️</span>
                    <div>
                        <h3 class="font-bold text-base mb-1" style="color: var(--teal)">
                            {{ __('db.One-time fee. No matchmaker. No agent. No hidden charges.') }}
                        </h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ __("db.This small, one-time fee is not a service charge — it's how we keep Sallaamti's Nikah section safe. Pakistan has seen too many families lose time and trust to fake profiles and paid 'rishta' agents online. This fee filters out fake accounts and confirms every profile belongs to a real, serious family — so once verified, you speak directly with the interested family, with no middleman in between.") }}
                        </p>
                        <ul class="mt-3 space-y-1 text-sm text-gray-700">
                            <li>✅ {{ __('db.Charged only once — never again') }}</li>
                            <li>✅ {{ __('db.Zero matchmaker or agent commission') }}</li>
                            <li>✅ {{ __('db.Direct contact with the interested family') }}</li>
                            <li>✅ {{ __('db.Your CNIC and payment details are never shown publicly') }}</li>
                        </ul>
                        <p class="mt-3 text-xs text-gray-500">
                            {{ __("db.This is our small way of helping the Pakistani community make Nikah easy, honest, and safe — Insha'Allah.") }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-2">Fee Amount: Rs. {{ number_format($profile->payment_status === 'confirmed' ? $profile->payment_amount : setting('nikah_verification_fee', config('services.nikah.verification_fee'))) }}</h3>
                <p class="text-sm text-gray-500 mb-4">A verification fee confirms your serious intent and helps us maintain a trustworthy matchmaking community. Please send the amount via JazzCash or EasyPaisa to the number below, then submit your payment details for confirmation.</p>

                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm mb-6 space-y-4">
                    @if (setting('jazzcash_number'))
                    <div>
                        <p class="font-bold mb-1" style="color: var(--gold)">📱 JazzCash</p>
                        <img src="{{ asset('images/jazzcash.png') }}" alt="JazzCash" class="h-8 w-auto mb-1">
                        <p class="text-gray-600 mb-0">{{ setting('jazzcash_number') }}</p>
                        <p class="font-semibold text-gray-700 mb-0">{{ setting('jazzcash_account_title') }}</p>
                    </div>
                    @endif
                    @if (setting('bank_name'))
                    <div>
                        <p class="font-bold mb-1" style="color: var(--gold)">🏦 Bank Transfer</p>
                        <img src="{{ asset('images/meezan.png') }}" alt="Bank" class="h-16 w-auto mb-1">
                        <p class="text-gray-600 text-sm mb-0">Bank: {{ setting('bank_name') }}</p>
                        <p class="text-gray-600 text-sm mb-0">Account Title: {{ setting('bank_account_title') }}</p>
                        <p class="text-gray-600 text-sm mb-0">Account No: {{ setting('bank_account_number') }}</p>
                        <p class="text-gray-600 text-sm mb-0">IBAN: {{ setting('bank_account_iban') }}</p>
                    </div>
                    @endif
                    @if (!setting('jazzcash_number') && !setting('bank_name'))
                    <p class="text-red-600">Payment details have not been configured yet. Please contact support before sending any payment.</p>
                    @endif
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