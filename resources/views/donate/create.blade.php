<x-guest-layout>

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden flex items-center"
        style="min-height: 320px; background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%);">
        <div class="absolute inset-0 opacity-5 flex items-center justify-center pointer-events-none"
            style="font-size: 20rem; color: #fff;">🤲</div>
        <div class="max-w-4xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.Sadaqah Jariyah') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">{{ __('db.Support Sallaamti') }}</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                {{ __("db.\"The believer's shade on the Day of Resurrection will be his charity.\" — Tirmidhi") }}
            </p>

            {{-- Quick impact stats --}}
            <div class="flex justify-center gap-6 mt-8 flex-wrap">
                @foreach ([
                ['📚', \App\Models\Enrollment::count() . '+', __('db.Students Helped')],
                ['🎓', \App\Models\Certificate::count() . '+', __('db.Certificates Given')],
                ['🌍', '20+', __('db.Countries Reached')],
                ] as $s)
                <div class="text-center">
                    <div class="text-2xl mb-1">{{ $s[0] }}</div>
                    <div class="text-xl font-extrabold text-white">{{ $s[1] }}</div>
                    <div class="text-white/50 text-xs">{{ $s[2] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT --}}
    {{-- ============================================================ --}}
    <section class="py-16 bg-cream">
        <div class="max-w-4xl mx-auto px-4">

            <div class="grid lg:grid-cols-5 gap-8">

                {{-- ===== FORM (wider) ===== --}}
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-3xl shadow-sm p-8">

                        <h3 class="font-extrabold text-2xl text-gray-800 mb-2">{{ __('db.Make a Donation') }}</h3>
                        <p class="text-gray-400 text-sm mb-8">{{ __('db.3 simple steps — choose amount, pay, upload screenshot.') }}</p>

                        @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl text-red-600 text-sm"
                            style="background: #fef2f2; border: 1px solid #fecaca">
                            <p class="font-semibold mb-1">{{ __('db.Please fix the following:') }}</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('donate.store') }}"
                            enctype="multipart/form-data" class="space-y-7">
                            @csrf
                            {{-- Honeypot: hidden from real visitors, bots that auto-fill every field will trip it --}}
                            <div class="absolute -left-[9999px]" aria-hidden="true">
                                <label for="website">Leave this field empty</label>
                                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                            </div>

                            {{-- STEP 1: Amount --}}
                            <fieldset>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                        style="background: var(--teal)">1</div>
                                    <legend class="font-bold text-gray-800">{{ __('db.Choose an Amount') }}</legend>
                                </div>

                                {{-- Quick amounts --}}
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    @foreach ([
                                    ['500', __('db.PKR 500'), __('db.Covers 1 week of lessons')],
                                    ['1000', __('db.PKR 1,000'), __('db.2 weeks of a student')],
                                    ['3000', __('db.PKR 3,000'), __('db.1 month education')],
                                    ['5000', __('db.PKR 5,000'), __('db.Support a family')],
                                    ['10000', __('db.PKR 10,000'), __('db.Sponsor a course')],
                                    ['custom', __('db.Custom'), __('db.Any amount helps')],
                                    ] as $amt)
                                    <button type="button"
                                        onclick="selectAmount('{{ $amt[0] }}')"
                                        id="btn-{{ $amt[0] }}"
                                        class="amount-preset group relative p-3 rounded-xl border-2 border-gray-200 text-center transition-all duration-200 hover:border-teal-500 focus:outline-none">
                                        <span class="block font-bold text-gray-800 text-sm">{{ $amt[1] }}</span>
                                        <span class="block text-gray-400 text-xs mt-0.5 leading-tight">{{ $amt[2] }}</span>
                                    </button>
                                    @endforeach
                                </div>

                                {{-- Custom amount input --}}
                                <div id="customAmountWrap" class="hidden">
                                    <div class="flex items-center gap-2 border-2 border-teal-500 rounded-xl px-4 py-3">
                                        <span class="text-gray-500 font-semibold text-sm flex-shrink-0">PKR</span>
                                        <input type="number" id="customAmountInput"
                                            class="flex-1 outline-none text-gray-800 text-sm font-medium bg-transparent"
                                            placeholder="{{ __('db.Enter amount e.g. 2500') }}"
                                            min="100">
                                    </div>
                                </div>

                                {{-- Hidden actual amount field --}}
                                <input type="hidden" name="amount" id="amountFinal" value="{{ old('amount') }}">
                                @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            {{-- STEP 2: Payment Method --}}
                            <fieldset>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                        style="background: var(--teal)">2</div>
                                    <legend class="font-bold text-gray-800">{{ __('db.Pay Using') }}</legend>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-4" x-data="{ method: '{{ old('payment_method', '') }}' }">

                                    {{-- Bank Transfer --}}
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="bank_transfer"
                                            x-model="method"
                                            {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div class="peer-checked:border-teal-600 peer-checked:bg-teal-50 border-2 border-gray-200 rounded-2xl p-4 text-center transition-all cursor-pointer hover:border-teal-400">
                                            <div class="text-2xl mb-1">🏦</div>
                                            <p class="font-bold text-sm text-gray-800">{{ __('db.Bank Transfer') }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ __('db.Pakistan') }}</p>
                                        </div>
                                    </label>

                                    {{-- International --}}
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="international"
                                            x-model="method"
                                            {{ old('payment_method') === 'international' ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div class="peer-checked:border-teal-600 peer-checked:bg-teal-50 border-2 border-gray-200 rounded-2xl p-4 text-center transition-all cursor-pointer hover:border-teal-400">
                                            <div class="text-2xl mb-1">🌐</div>
                                            <p class="font-bold text-sm text-gray-800">{{ __('db.International') }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ __('db.Wire / SWIFT') }}</p>
                                        </div>
                                    </label>

                                    {{-- Payment details shown dynamically --}}
                                    <div x-show="method === 'bank_transfer'" x-transition
                                        class="col-span-2 rounded-2xl p-4 text-sm space-y-1.5"
                                        style="background: #e8f5f5; border: 1px solid #0d6b6b33">
                                        <p class="font-bold text-gray-700 mb-2">🏦 {{ __('db.Bank Transfer Details') }}</p>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Bank') }}</span>
                                            <span class="font-medium text-gray-800">{{ setting('bank_name', 'Meezan Bank') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Account No') }}</span>
                                            <span class="font-medium text-gray-800 select-all">{{ setting('bank_account_number', 'XXXX-XXXXXXXXXXXX') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.IBAN') }}</span>
                                            <span class="font-medium text-gray-800 select-all text-xs">{{ setting('bank_account_iban', 'PK00MEZN000000000000') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Account Title') }}</span>
                                            <span class="font-medium text-gray-800">{{ setting('site_name', 'Sallaamti') }}</span>
                                        </div>
                                    </div>

                                    <div x-show="method === 'international'" x-transition
                                        class="col-span-2 rounded-2xl p-4 text-sm space-y-1.5"
                                        style="background: #fdf6e3; border: 1px solid #b8962e33">
                                        <p class="font-bold text-gray-700 mb-2">🌐 {{ __('db.International Wire Details') }}</p>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Bank') }}</span>
                                            <span class="font-medium text-gray-800">{{ setting('bank_name', 'Meezan Bank') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.IBAN / SWIFT') }}</span>
                                            <span class="font-medium text-gray-800 text-xs select-all">{{ setting('bank_account_iban', 'PK00MEZN000000000000') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Account Title') }}</span>
                                            <span class="font-medium text-gray-800">{{ setting('site_name', 'Sallaamti') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('db.Country') }}</span>
                                            <span class="font-medium text-gray-800">{{ __('db.Pakistan') }}</span>
                                        </div>
                                        <p class="text-xs text-amber-600 mt-2 pt-2 border-t border-amber-200">
                                            {{ __('db.For international transfers contact us on WhatsApp for routing details.') }}
                                        </p>
                                    </div>
                                </div>

                                @error('payment_method')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            {{-- STEP 3: Proof --}}
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                        style="background: var(--teal)">3</div>
                                    <label for="screenshot" class="font-bold text-gray-800">{{ __('db.Upload Payment Screenshot (Optional)') }}</label>
                                </div>

                                <div id="uploadArea"
                                    class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center cursor-pointer transition-all hover:border-teal-400 hover:bg-teal-50"
                                    onclick="document.getElementById('screenshot').click()">
                                    <div id="uploadPlaceholder">
                                        <div class="text-4xl mb-2">📸</div>
                                        <p class="text-gray-600 text-sm font-medium">{{ __('db.Tap to upload payment screenshot') }}</p>
                                        <p class="text-gray-400 text-xs mt-1">{{ __('db.JPG or PNG — Max 4MB') }}</p>
                                    </div>
                                    <div id="uploadPreview" class="hidden">
                                        <img id="previewImg" src="" alt="" class="max-h-40 mx-auto rounded-xl mb-2">
                                        <p id="uploadedName" class="text-sm font-semibold" style="color: var(--teal)"></p>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('db.Tap to change') }}</p>
                                    </div>
                                </div>
                                <input type="file" id="screenshot" name="payment_screenshot"
                                    accept="image/*" class="hidden">
                                @error('payment_screenshot')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Optional: name only --}}
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-xs text-gray-400 mb-3 font-medium uppercase tracking-wider">{{ __('db.Optional — helps us send you a receipt') }}</p>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="donor_name" class="sr-only">{{ __('db.Your name') }}</label>
                                        <div class="flex items-center border border-gray-200 rounded-xl px-3 py-2.5 gap-2 focus-within:border-teal-500 transition-colors">
                                            <i class="fa fa-user text-gray-300 text-xs w-4"></i>
                                            <input id="donor_name" type="text" name="donor_name"
                                                value="{{ old('donor_name', auth()->user()?->name) }}"
                                                class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                                                placeholder="{{ __('db.Your name') }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="donor_email" class="sr-only">{{ __('db.Email for receipt') }}</label>
                                        <div class="flex items-center border border-gray-200 rounded-xl px-3 py-2.5 gap-2 focus-within:border-teal-500 transition-colors">
                                            <i class="fa fa-envelope text-gray-300 text-xs w-4"></i>
                                            <input id="donor_email" type="email" name="donor_email"
                                                value="{{ old('donor_email', auth()->user()?->email) }}"
                                                class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                                                placeholder="{{ __('db.Email for receipt') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden fields with defaults --}}
                            <input type="hidden" name="cause" value="general">
                            <input type="hidden" name="payment_reference" value="pending-admin-verify">

                            {{-- Anonymous toggle --}}
                            <div class="flex items-center gap-3 py-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_anonymous" value="1" class="sr-only peer"
                                        {{ old('is_anonymous') ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-600"></div>
                                </label>
                                <span class="text-sm text-gray-500">{{ __('db.Keep my donation anonymous') }}</span>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full py-4 rounded-2xl text-white font-extrabold text-lg transition-all duration-200 hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%)">
                                💝 {{ __('db.Submit Donation — JazakAllah Khair') }}
                            </button>

                            <p class="text-center text-xs text-gray-400">
                                {{ __('db.Our admin will verify and confirm within 24 hours, in sha Allah.') }}
                            </p>

                        </form>
                    </div>
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Why donate --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-4">🤲 {{ __('db.Your Donation Helps') }}</h5>
                        <div class="space-y-3">
                            @foreach ([
                            ['📚', __('db.Quran education'), __('db.Free Quran lessons for orphans and needy children')],
                            ['👨‍👩‍👧', __('db.Family support'), __('db.Counseling and assistance for struggling families')],
                            ['🌐', __('db.Platform free'), __('db.Keeping Sallaamti accessible for students globally')],
                            ['💍', __('db.Nikah support'), __('db.Helping deserving singles afford the verification process')],
                            ] as $impact)
                            <div class="flex items-start gap-3">
                                <span class="text-xl flex-shrink-0">{{ $impact[0] }}</span>
                                <div>
                                    <p class="font-semibold text-gray-700 text-sm">{{ $impact[1] }}</p>
                                    <p class="text-gray-400 text-xs mt-0.5">{{ $impact[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Trust signals --}}
                    <div class="rounded-2xl p-6" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
                        <h5 class="font-bold text-white mb-4">🔒 {{ __('db.100% Safe & Trusted') }}</h5>
                        <div class="space-y-2.5">
                            @foreach ([
                            __('db.Every donation manually verified'),
                            __('db.Receipt emailed within 24 hours'),
                            __('db.Full transparency on fund use'),
                            __('db.No admin salary taken from donations'),
                            ] as $trust)
                            <div class="flex items-center gap-2 text-white/70 text-sm">
                                <svg class="w-4 h-4 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $trust }}
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- WhatsApp support --}}
                    <a href="https://wa.me/{{ setting('social_whatsapp') }}" target="_blank"
                        class="flex items-center gap-3 bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                            style="background: #f0fdf4">💬</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm group-hover:text-green-600 transition-colors">
                                {{ __('db.Need help? WhatsApp us') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('db.We reply within a few hours') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                </div>
            </div>
        </div>
    </section>

    <style>
        .amount-preset.selected {
            border-color: var(--teal);
            background: var(--teal-light);
        }

        .amount-preset.selected span:first-child {
            color: var(--teal);
        }
    </style>

    <script>
        function selectAmount(val) {
            // Remove selected from all
            document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('selected'));

            const customWrap = document.getElementById('customAmountWrap');
            const finalInput = document.getElementById('amountFinal');

            if (val === 'custom') {
                // Show custom input
                customWrap.classList.remove('hidden');
                document.getElementById('btn-custom').classList.add('selected');
                finalInput.value = '';

                // Sync custom input to hidden field
                const customInput = document.getElementById('customAmountInput');
                customInput.focus();
                customInput.addEventListener('input', () => {
                    finalInput.value = customInput.value;
                });
            } else {
                customWrap.classList.add('hidden');
                document.getElementById('btn-' + val).classList.add('selected');
                finalInput.value = val;
            }
        }

        // Screenshot preview
        document.getElementById('screenshot').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('uploadPreview').classList.remove('hidden');
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('uploadedName').textContent = file.name;
            };
            reader.readAsDataURL(file);
        });

        // Pre-select first amount on load
        document.addEventListener('DOMContentLoaded', () => {
            @if(old('amount'))
            document.getElementById('amountFinal').value = '{{ old('
            amount ') }}';
            @else
            selectAmount('1000');
            @endif
        });
    </script>

    <x-faq-section module="donation" />
</x-guest-layout>