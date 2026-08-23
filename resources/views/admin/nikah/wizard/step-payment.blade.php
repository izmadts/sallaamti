<x-dynamic-component :component="$routePrefix === 'matchmaker' ? 'matchmaker-layout' : 'admin-layout'">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route($routePrefix === 'matchmaker' ? 'matchmaker.nikah.index' : 'admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Create Profile</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-medium text-sm mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'payment') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    @if ($expectedFee <= 0)
                    <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                        ✅ No verification fee applies to this client — nothing to collect here. Press Next to continue.
                    </div>
                    @else
                    <x-nikah-section title="Verification Fee Payment (optional)" icon="💳" color="emerald" description="Collecting payment right now? Submit it here — it goes straight into the admin review queue, same as if the client submitted it themselves. Not paying today? Leave this blank and press Next; they can pay later from their own account, or you can submit it afterward from this profile's page.">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Fee: Rs. {{ number_format($expectedFee) }}</p>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm mb-4 space-y-2">
                            @if (setting('jazzcash_number'))
                            <p><strong>📱 JazzCash:</strong> {{ setting('jazzcash_number') }} ({{ setting('jazzcash_account_title') }})</p>
                            @endif
                            @if (setting('bank_name'))
                            <p><strong>🏦 Bank:</strong> {{ setting('bank_name') }} — {{ setting('bank_account_title') }} — {{ setting('bank_account_number') }}</p>
                            @endif
                            @if (!setting('jazzcash_number') && !setting('bank_name'))
                            <p class="text-red-600">Payment details have not been configured in Settings yet.</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="payment_method" value="Payment Method" />
                                <select id="payment_method" name="payment_method" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Not paid yet — skip</option>
                                    <option value="jazzcash" {{ old('payment_method', $data['payment_method'] ?? '') === 'jazzcash' ? 'selected' : '' }}>JazzCash</option>
                                    <option value="bank_transfer" {{ old('payment_method', $data['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_reference" value="Reference / Transaction ID (optional)" />
                                <x-text-input id="payment_reference" name="payment_reference" type="text" class="w-full mt-1" :value="old('payment_reference', $data['payment_reference'] ?? '')" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="payment_screenshot" value="Payment Screenshot / Receipt Photo" />
                            <input id="payment_screenshot" name="payment_screenshot" type="file" accept="image/*" capture="environment" class="w-full mt-1">
                            @if (!empty($data['payment_screenshot']))
                            <p class="text-xs text-green-600 mt-1">✓ Already uploaded — choose a file only if you want to replace it.</p>
                            @endif
                        </div>
                    </x-nikah-section>
                    @endif

                    <div class="flex justify-between pt-2">
                        <a href="{{ route($routePrefix . '.nikah.profiles.create.step', 'verification') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Review & Confirm →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-dynamic-component>
