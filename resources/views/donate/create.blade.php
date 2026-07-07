<x-guest-layout>

    {{-- Page Hero --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 280px; background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">Support Our Mission</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">💝 Make a Donation</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">"Spend in the way of Allah and do not throw yourselves into destruction." — Quran 2:195</p>
        </div>
    </section>

    <section class="py-16 bg-cream">
        <div class="max-w-5xl mx-auto px-4">

            @if (session('status'))
            <div class="mb-6 p-4 rounded-xl text-green-700 flex items-center gap-3" style="background: #f0fdf4; border: 1px solid #bbf7d0">
                <i class="fa fa-check-circle text-xl"></i>
                <div>{{ session('status') }}</div>
            </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">

                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-8">
                        <h3 class="font-bold text-xl text-gray-800 mb-6">Donation Details</h3>

                        @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl text-red-700 text-sm" style="background: #fef2f2; border: 1px solid #fecaca">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('donate.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            {{-- Cause --}}
                            <div>
                                <label class="auth-label">Select a Cause</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-2">
                                    @foreach ([
                                    ['quran_education', '📚', 'Quran Education'],
                                    ['family_support', '👨‍👩‍👧', 'Family Support'],
                                    ['skills_training', '💻', 'Skills Training'],
                                    ['nikah_support', '💍', 'Nikah Support'],
                                    ['general', '🕌', 'General Fund'],
                                    ['where_needed', '🤲', 'Where Needed Most'],
                                    ] as $cause)
                                    <label class="cause-option">
                                        <input type="radio" name="cause" value="{{ $cause[0] }}" {{ old('cause', 'general') === $cause[0] ? 'checked' : '' }} class="sr-only cause-radio">
                                        <span class="cause-label">
                                            <span class="text-xl">{{ $cause[1] }}</span>
                                            <span class="text-xs font-semibold mt-1">{{ $cause[2] }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div>
                                <label class="auth-label">Donation Amount</label>
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-2 mb-3">
                                    @foreach (['500', '1000', '2000', '5000', '10000'] as $amt)
                                    <button type="button" onclick="setAmount({{ $amt }})"
                                        class="amount-btn py-2 px-3 rounded-xl border-2 text-sm font-semibold transition-all duration-200 text-gray-600 border-gray-200 hover:border-teal-600 hover:text-teal-700">
                                        PKR {{ number_format($amt) }}
                                    </button>
                                    @endforeach
                                </div>
                                <div class="auth-input-wrap">
                                    <span class="auth-icon font-semibold text-gray-500 text-sm">PKR</span>
                                    <input type="number" name="amount" id="amountInput" value="{{ old('amount') }}" required
                                        class="auth-input @error('amount') auth-input-error @enderror"
                                        placeholder="Or enter custom amount">
                                </div>
                                <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                            </div>

                            {{-- Donor name --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="auth-label">Your Name</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-user auth-icon"></i>
                                        <input type="text" name="donor_name" value="{{ old('donor_name', auth()->user()?->name) }}" required
                                            class="auth-input" placeholder="Full name">
                                    </div>
                                </div>
                                <div>
                                    <label class="auth-label">Email Address</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-envelope auth-icon"></i>
                                        <input type="email" name="donor_email" value="{{ old('donor_email', auth()->user()?->email) }}" required
                                            class="auth-input" placeholder="your@email.com">
                                    </div>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="auth-label">Phone / WhatsApp <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-phone auth-icon"></i>
                                    <input type="text" name="donor_phone" value="{{ old('donor_phone', auth()->user()?->phone) }}"
                                        class="auth-input" placeholder="03XX XXXXXXX">
                                </div>
                            </div>

                            {{-- Payment Method --}}
                            <div>
                                <label class="auth-label">Payment Method</label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-credit-card auth-icon"></i>
                                    <select name="payment_method" required class="auth-input @error('payment_method') auth-input-error @enderror">
                                        <option value="">Select payment method</option>
                                        <option value="jazzcash" {{ old('payment_method') === 'jazzcash' ? 'selected' : '' }}>💚 JazzCash</option>
                                        <!-- <option value="easypaisa" {{ old('payment_method') === 'easypaisa' ? 'selected' : '' }}>🟡 EasyPaisa</option> -->
                                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                        <option value="international" {{ old('payment_method') === 'international' ? 'selected' : '' }}>🌐 International Wire</option>
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-1" />
                            </div>

                            {{-- Transaction Reference --}}
                            <div>
                                <label class="auth-label">Transaction Reference / ID</label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-hashtag auth-icon"></i>
                                    <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required
                                        class="auth-input @error('payment_reference') auth-input-error @enderror"
                                        placeholder="e.g. TXN-123456789">
                                </div>
                                <x-input-error :messages="$errors->get('payment_reference')" class="mt-1" />
                            </div>

                            {{-- Screenshot --}}
                            <div>
                                <label class="auth-label">Payment Screenshot</label>
                                <div class="upload-area" onclick="document.getElementById('screenshot').click()">
                                    <div class="text-3xl mb-2">📸</div>
                                    <p class="text-gray-600 text-sm font-medium">Click to upload payment screenshot</p>
                                    <p class="text-gray-400 text-xs mt-1">JPG, PNG — Max 4MB</p>
                                    <p id="file-name" class="text-xs mt-2 font-semibold" style="color: var(--teal)"></p>
                                </div>
                                <input type="file" id="screenshot" name="payment_screenshot" accept="image/*" class="hidden"
                                    onchange="document.getElementById('file-name').textContent = this.files[0]?.name || ''" required>
                                <x-input-error :messages="$errors->get('payment_screenshot')" class="mt-1" />
                            </div>

                            {{-- Message --}}
                            <div>
                                <label class="auth-label">Message <span class="text-gray-400 font-normal">(optional)</span></label>
                                <textarea name="message" rows="3"
                                    class="auth-input w-full resize-none"
                                    style="padding-left: 1rem"
                                    placeholder="A message or du'a you'd like to share...">{{ old('message') }}</textarea>
                            </div>

                            {{-- Anonymous --}}
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_anonymous" value="1" id="anon" class="auth-checkbox" {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label for="anon" class="text-sm text-gray-600">Make my donation anonymous (your name won't be shown publicly)</label>
                            </div>

                            <button type="submit" class="btn-base btn-gold w-full py-4 text-base font-bold">
                                💝 Submit Donation <i class="fa fa-arrow-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">

                    {{-- Payment Info --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-4">💳 Payment Details</h5>
                        <div class="space-y-3 text-sm">
                            <div class="p-3 rounded-xl" style="background: var(--teal-light)">
                                <p class="font-bold mb-0.5" style="color: var(--teal)">💚 JazzCash / EasyPaisa</p>
                                <p class="text-gray-600 mb-0">{{ setting('jazzcash_number', '03XX-XXXXXXX') }}</p>
                                <p class="font-semibold text-gray-700 mb-0">{{ setting('site_name', 'Sallaamti') }}</p>
                            </div>
                            <div class="p-3 rounded-xl" style="background: #fdf6e3">
                                <p class="font-bold mb-0.5" style="color: var(--gold)">🏦 Bank Transfer</p>
                                <p class="text-gray-600 text-xs mb-0">Bank: {{ setting('bank_name', 'Meezan Bank') }}</p>
                                <p class="text-gray-600 text-xs mb-0">IBAN: {{ setting('bank_iban', 'PK00MEZN000XXXXXXXXX') }}</p>
                                <p class="text-gray-600 text-xs mb-0">Title: {{ setting('site_name', 'Sallaamti') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Causes --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-4">🤲 Your Donation Helps</h5>
                        <div class="space-y-3 text-sm">
                            @foreach ([
                            ['📚', 'Quran education for orphans and needy students'],
                            ['👨‍👩‍👧', 'Supporting families in financial hardship'],
                            ['🛠️', 'Skills training for unemployed youth'],
                            ['🌐', 'Keeping our platform free for students globally'],
                            ] as $impact)
                            <div class="flex items-start gap-2 text-gray-600">
                                <span class="flex-shrink-0">{{ $impact[0] }}</span>
                                <span>{{ $impact[1] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Trust --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-4">🔒 Safe & Transparent</h5>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2"><i class="fa fa-check-circle" style="color: var(--teal)"></i> Manual verification by admin</div>
                            <div class="flex items-center gap-2"><i class="fa fa-check-circle" style="color: var(--teal)"></i> Receipt sent to your email</div>
                            <div class="flex items-center gap-2"><i class="fa fa-check-circle" style="color: var(--teal)"></i> Transparent fund utilization</div>
                            <div class="flex items-center gap-2"><i class="fa fa-check-circle" style="color: var(--teal)"></i> 100% goes to the cause</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <style>
        /* Cause selector */
        .cause-option {
            cursor: pointer;
        }

        .cause-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 8px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
            text-align: center;
            height: 100%;
            color: #6b7280;
            font-size: 12px;
        }

        .cause-radio:checked+.cause-label {
            border-color: var(--teal);
            background: var(--teal-light);
            color: var(--teal);
        }

        .cause-label:hover {
            border-color: var(--teal);
        }

        /* Amount buttons */
        .amount-btn.active {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff;
        }

        /* Upload area */
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-area:hover {
            border-color: var(--teal);
            background: var(--teal-light);
        }
    </style>

    <script>
        function setAmount(val) {
            document.getElementById('amountInput').value = val;
            document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>

</x-guest-layout>