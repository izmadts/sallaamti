<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('db.Verification Fee Payment') }}</h2>
    </x-slot>

    @php
        $feeAmount = $profile->payment_status === 'confirmed' ? $profile->payment_amount : $profile->applicableVerificationFee();
        $whatsappMessage = "Assalam-o-Alaikum, I'm " . auth()->user()->name . " (Sallaamti Profile #" . $profile->id . "). I've paid the Rs. " . number_format($feeAmount) . " Nikah verification fee. Sending my payment receipt here for confirmation.";
    @endphp

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (in_array($profile->payment_status, ['unpaid', 'rejected']))
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full text-white" style="background: var(--teal)">
                    🎯 {{ __('db.Final Step') }}
                </span>
                <span class="text-sm text-gray-500">{{ __('db.This is the only thing standing between you and a live, searchable profile.') }}</span>
            </div>
            @endif

            <div class="rounded-xl border-2 p-5" style="border-color: var(--gold); background: linear-gradient(135deg, #fffbeb 0%, #f0fdfa 100%)">
                <div class="flex items-start gap-3">
                    <span class="text-2xl shrink-0">🛡️</span>
                    <div>
                        <h3 class="font-bold text-base mb-1" style="color: var(--teal)">
                            {{ __('db.One-time fee. No Nikah Counselor. No agent. No hidden charges.') }}
                        </h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ __("db.This small, one-time fee is not a service charge — it's how we keep Sallaamti's Nikah section safe. Pakistan has seen too many families lose time and trust to fake profiles and paid 'rishta' agents online. This fee filters out fake accounts and confirms every profile belongs to a real, serious family — so once verified, you speak directly with the interested family, with no middleman in between.") }}
                        </p>
                        <ul class="mt-3 space-y-1 text-sm text-gray-700">
                            <li>✅ {{ __('db.Charged only once — never again') }}</li>
                            <li>✅ {{ __('db.Zero Nikah Counselor or agent commission') }}</li>
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
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('db.Fee Amount:') }} {{ __('db.Rs. :amount', ['amount' => number_format($feeAmount)]) }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.Send this amount via JazzCash or Bank Transfer to the details below, then either submit your receipt on this page or send it to us directly on WhatsApp — whichever is easier for you.') }}</p>

                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm mb-6 space-y-4">
                    @if (setting('jazzcash_number'))
                    <div>
                        <p class="font-bold mb-1" style="color: var(--gold)">📱 {{ __('db.JazzCash') }}</p>
                        <img src="{{ asset('images/jazzcash.png') }}" alt="{{ __('db.JazzCash') }}" class="h-8 w-auto mb-1">
                        <p class="text-gray-600 mb-0 flex items-center gap-1">
                            {{ setting('jazzcash_number') }}
                            <x-copy-button :value="setting('jazzcash_number')" />
                        </p>
                        <p class="font-semibold text-gray-700 mb-0">{{ setting('jazzcash_account_title') }}</p>
                    </div>
                    @endif
                    @if (setting('bank_name'))
                    <div>
                        <p class="font-bold mb-1" style="color: var(--gold)">🏦 {{ __('db.Bank Transfer') }}</p>
                        <img src="{{ asset('images/meezan.png') }}" alt="{{ __('db.Bank') }}" class="h-16 w-auto mb-1">
                        <p class="text-gray-600 text-sm mb-0">{{ __('db.Bank:') }} {{ setting('bank_name') }}</p>
                        <p class="text-gray-600 text-sm mb-0">{{ __('db.Account Title:') }} {{ setting('bank_account_title') }}</p>
                        <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                            {{ __('db.Account No:') }} {{ setting('bank_account_number') }}
                            <x-copy-button :value="setting('bank_account_number')" />
                        </p>
                        <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                            {{ __('db.IBAN:') }} {{ setting('bank_account_iban') }}
                            <x-copy-button :value="setting('bank_account_iban')" />
                        </p>
                    </div>
                    @endif
                    @if (!setting('jazzcash_number') && !setting('bank_name'))
                    <p class="text-red-600">{{ __('db.Payment details have not been configured yet. Please contact support before sending any payment.') }}</p>
                    @endif
                </div>

                @if (setting('social_whatsapp'))
                <a href="https://wa.me/{{ setting('social_whatsapp') }}?text={{ urlencode($whatsappMessage) }}" target="_blank"
                    class="flex items-center gap-3 rounded-xl p-4 mb-6 transition hover:shadow-md" style="background: #f0fdf4; border: 1px solid #bbf7d0">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-white">💬</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ __('db.Prefer not to fill this form? Send your receipt on WhatsApp instead') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('db.Tap here, send us the payment screenshot, and our team will confirm it manually — just as valid as submitting online.') }}</p>
                    </div>
                </a>
                @endif

                @if ($profile->payment_status === 'submitted')
                <div class="p-4 bg-yellow-50 text-yellow-700 rounded text-sm">
                    ⏳ {{ __('db.Your payment proof has been submitted and is awaiting confirmation by our team.') }}
                </div>
                @elseif ($profile->payment_status === 'confirmed')
                <div class="p-4 bg-green-50 text-green-700 rounded text-sm">
                    ✅ {{ __('db.Payment confirmed. Your profile will now proceed to CNIC verification.') }}
                </div>
                @else
                @if ($profile->payment_status === 'rejected')
                <div class="p-4 bg-red-50 text-red-700 rounded text-sm mb-4">
                    ❌ {{ __('db.Your previous payment proof was rejected. Reason:') }} {{ $profile->payment_rejection_reason }}
                    <br>{{ __('db.Please resubmit below.') }}
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label :value="__('db.Payment Method')" />
                        <select name="payment_method" required class="border-gray-300 rounded-md w-full mt-1"
                            title="{{ __('db.Choose whichever method you used to send the fee.') }}">
                            <option value="jazzcash">{{ __('db.JazzCash') }}</option>
                            <option value="bank_transfer">{{ __('db.Bank Transfer') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Payment Screenshot')" />
                        <input type="file" name="payment_screenshot" accept="image/*" capture="environment" class="w-full mt-1" required
                            title="{{ __('db.A screenshot of your JazzCash/bank confirmation, or a photo of a printed receipt.') }}">
                        <p class="text-[11px] text-gray-400 mt-1">📷 {{ __('db.You can take a photo directly on mobile, or upload a saved screenshot.') }}</p>
                    </div>
                    <x-primary-button>✅ {{ __('db.Complete My Verification — Submit Payment') }}</x-primary-button>
                </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
