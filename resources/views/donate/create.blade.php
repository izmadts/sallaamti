<x-guest-layout>
    <div class="max-w-xl mx-auto py-12 px-4">
        <h2 class="text-2xl font-semibold mb-2">Donate to Sallaamti</h2>
        <p class="text-gray-500 mb-6">Your contribution supports Quran education, family support, and community welfare programs.</p>

        <form method="POST" action="{{ route('donate.store') }}" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded-lg shadow-sm">
            @csrf
            <div><x-input-label value="Your Name" /><x-text-input name="donor_name" class="w-full mt-1" required /></div>
            <div class="grid grid-cols-2 gap-4">
                <div><x-input-label value="Email (optional)" /><x-text-input name="email" type="email" class="w-full mt-1" /></div>
                <div><x-input-label value="Phone (optional)" /><x-text-input name="phone" class="w-full mt-1" /></div>
            </div>
            <div><x-input-label value="Donation Amount (Rs.)" /><x-text-input name="amount" type="number" step="0.01" class="w-full mt-1" required /></div>
            <div>
                <x-input-label value="Purpose" />
                <select name="purpose" class="border-gray-300 rounded-md w-full mt-1">
                    <option value="General">General / Wherever needed most</option>
                    <option value="Quran Education">Quran Education</option>
                    <option value="Orphan & Needy Support">Orphan & Needy Support</option>
                    <option value="Nikah Fund">Nikah / Marriage Support</option>
                    <option value="Mosque & Facilities">Mosque & Facilities</option>
                </select>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm">
                <p><strong>JazzCash / EasyPaisa:</strong> 03XX-XXXXXXX</p>
                <p><strong>Account Title:</strong> Sallaamti</p>
            </div>

            <div>
                <x-input-label value="Payment Method" />
                <select name="payment_method" class="border-gray-300 rounded-md w-full mt-1" required>
                    <option value="jazzcash">JazzCash</option>
                    <option value="easypaisa">EasyPaisa</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div><x-input-label value="Transaction Reference" /><x-text-input name="payment_reference" class="w-full mt-1" required /></div>
            <div><x-input-label value="Payment Screenshot" /><input type="file" name="payment_screenshot" accept="image/*" class="w-full mt-1" required></div>

            <x-primary-button>Submit Donation</x-primary-button>
        </form>
    </div>
</x-guest-layout>