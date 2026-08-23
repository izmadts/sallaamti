{{-- Reusable payment-submission content — included from both this
     profile's own page (matchmaker/nikah/show.blade.php) and the linked
     client's page (matchmaker/clients/show.blade.php). Expects $profile.
     Deliberately has no outer card wrapper — the two pages use different
     card conventions, so each include site wraps this itself. --}}
@php $fee = $profile->applicableVerificationFee(); @endphp
@if ($fee > 0)
<h3 class="font-semibold text-gray-700 mb-1">💳 Verification Fee Payment</h3>

@if ($profile->payment_status === 'confirmed')
<p class="text-sm bg-green-50 text-green-700 rounded-lg p-3">✅ Payment confirmed — Rs. {{ number_format($profile->payment_amount) }}.</p>
@else
@if ($profile->payment_status === 'submitted')
<p class="text-sm bg-yellow-50 text-yellow-700 rounded-lg p-3 mb-3">⏳ Payment proof submitted — awaiting admin confirmation.</p>
@elseif ($profile->payment_status === 'rejected')
<p class="text-sm bg-red-50 text-red-700 rounded-lg p-3 mb-3">❌ Previous payment proof was rejected: {{ $profile->payment_rejection_reason }}. Please resubmit below.</p>
@endif

<p class="text-xs text-gray-500 mb-3">Fee: Rs. {{ number_format($fee) }}. Submit their receipt here once collected — it goes straight into the admin review queue, same as if they submitted it themselves.</p>

<form method="POST" action="{{ route('matchmaker.nikah.payment.submit', $profile) }}" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end">
    @csrf
    <div>
        <label class="text-xs text-gray-500 block mb-1">Method</label>
        <select name="payment_method" required class="border-gray-300 rounded-lg text-sm">
            <option value="jazzcash">JazzCash</option>
            <option value="bank_transfer">Bank Transfer</option>
        </select>
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Reference (optional)</label>
        <input type="text" name="payment_reference" class="border-gray-300 rounded-lg text-sm">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Receipt Photo</label>
        <input type="file" name="payment_screenshot" accept="image/*" capture="environment" required class="text-sm">
    </div>
    <x-primary-button>Submit for Review</x-primary-button>
</form>
@endif
@endif
