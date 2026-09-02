<x-guest-layout :title="__('db.Become a Nikah Counselor')" :description="__('db.Join the Sallaamti Nikah Counselor network — help families find halal matches while earning commission-based income.')">

    {{-- Page Hero --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 overflow-hidden" style="font-size: 20rem; color: #fff; display: flex; align-items: center; justify-content: center; pointer-events: none;">💍</div>
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: #d8c48a">{{ __('db.Join Our Mission') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">{{ __('db.Become a Certified Sallaamti Nikah Counselor') }}</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">{{ __("db.Help families in your community find safe, halal matches through Sallaamti — represent us with trust, earn for the service you provide, and be part of building Pakistan's most ethical matrimonial network.") }}</p>
        </div>
    </section>

    <section class="py-16 bg-cream">
        <div class="max-w-6xl mx-auto px-4">

            @if (session('status'))
            <div class="mb-8 p-4 rounded-xl text-center text-white font-semibold" style="background: #0d6b6b">{{ session('status') }}</div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">

                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-8" style="border-top: 4px solid #b8962e">
                        <h3 class="font-bold text-xl text-gray-800 mb-1">{{ __('db.Nikah Counselor Application') }}</h3>
                        <p class="text-sm text-gray-500 mb-6">درخواست فارم — تمام معلومات درست پُر کریں</p>

                        @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl text-red-700 text-sm" style="background: #fef2f2; border: 1px solid #fecaca">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('nikah-counselor.store') }}" enctype="multipart/form-data" class="space-y-5" x-data="{ payoutMethod: '{{ old('payout_method', '') }}' }">
                            @csrf
                            {{-- Honeypot --}}
                            <div class="absolute -left-[9999px]" aria-hidden="true">
                                <label for="website">Leave this field empty</label>
                                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="auth-label">{{ __('db.Full Name') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-user auth-icon"></i>
                                        <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required class="auth-input @error('full_name') auth-input-error @enderror" placeholder="{{ __('db.Your full name') }}">
                                    </div>
                                    <x-input-error :messages="$errors->get('full_name')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="guardian_name" class="auth-label">{{ __('db.Father / Husband / Guardian Name') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-user-shield auth-icon"></i>
                                        <input id="guardian_name" type="text" name="guardian_name" value="{{ old('guardian_name') }}" required class="auth-input @error('guardian_name') auth-input-error @enderror">
                                    </div>
                                    <x-input-error :messages="$errors->get('guardian_name')" class="mt-1" />
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="mobile_number" class="auth-label">{{ __('db.Mobile Number') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-phone auth-icon"></i>
                                        <input id="mobile_number" type="text" name="mobile_number" value="{{ old('mobile_number') }}" required class="auth-input @error('mobile_number') auth-input-error @enderror" placeholder="03XX XXXXXXX">
                                    </div>
                                    <x-input-error :messages="$errors->get('mobile_number')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="whatsapp_number" class="auth-label">{{ __('db.WhatsApp Number') }} <span class="text-gray-400 text-xs">{{ __('db.(if different)') }}</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fab fa-whatsapp auth-icon"></i>
                                        <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" class="auth-input">
                                    </div>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="gender" class="auth-label">{{ __('db.Gender') }} <span class="text-red-400">*</span></label>
                                    <select id="gender" name="gender" required class="auth-input @error('gender') auth-input-error @enderror" style="padding-left: 1rem">
                                        <option value="">{{ __('db.Select') }}</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="age" class="auth-label">{{ __('db.Age') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-birthday-cake auth-icon"></i>
                                        <input id="age" type="number" name="age" min="21" max="70" value="{{ old('age') }}" required class="auth-input @error('age') auth-input-error @enderror">
                                    </div>
                                    <x-input-error :messages="$errors->get('age')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="marital_status" class="auth-label">{{ __('db.Marital Status') }} <span class="text-red-400">*</span></label>
                                    <select id="marital_status" name="marital_status" required class="auth-input @error('marital_status') auth-input-error @enderror" style="padding-left: 1rem">
                                        <option value="">{{ __('db.Select') }}</option>
                                        @foreach (['never_married' => __('db.Never Married'), 'married' => __('db.Married'), 'divorced' => __('db.Divorced'), 'widowed' => __('db.Widowed'), 'separated' => __('db.Separated')] as $value => $label)
                                        <option value="{{ $value }}" {{ old('marital_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('marital_status')" class="mt-1" />
                                </div>
                            </div>

                            <div x-data="{ qualification: '{{ old('qualification') }}' }" class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="qualification" class="auth-label">{{ __('db.Qualification') }} <span class="text-red-400">*</span></label>
                                    <select id="qualification" name="qualification" x-model="qualification" required class="auth-input @error('qualification') auth-input-error @enderror" style="padding-left: 1rem">
                                        <option value="">{{ __('db.Select') }}</option>
                                        @foreach (\App\Models\MatchmakerApplication::QUALIFICATIONS as $value => $label)
                                        <option value="{{ $value }}" {{ old('qualification') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('qualification')" class="mt-1" />
                                </div>
                                <div x-show="qualification === 'other'" x-cloak>
                                    <label for="qualification_other" class="auth-label">{{ __('db.Please Specify') }}</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-graduation-cap auth-icon"></i>
                                        <input id="qualification_other" type="text" name="qualification_other" value="{{ old('qualification_other') }}" class="auth-input">
                                    </div>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="country" class="auth-label">{{ __('db.Country') }}</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-flag auth-icon"></i>
                                        <input id="country" type="text" name="country" value="{{ old('country', 'Pakistan') }}" class="auth-input">
                                    </div>
                                </div>
                                <div>
                                    <label for="area" class="auth-label">{{ __('db.City') }}</label>
                                    <x-searchable-select name="area" :options="\App\Support\PakistanCities::all()" :value="old('area')" placeholder="{{ __('db.Type to search or select') }}" class="auth-input" style="padding-left: 1rem" />
                                </div>
                                <div>
                                    <label for="address" class="auth-label">{{ __('db.Address') }}</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-home auth-icon"></i>
                                        <input id="address" type="text" name="address" value="{{ old('address') }}" class="auth-input">
                                    </div>
                                </div>
                            </div>

                            {{-- Identity documents --}}
                            <div class="p-4 rounded-xl" style="background: var(--cream); border: 1px solid #e5e5e5">
                                <p class="text-sm font-semibold text-gray-700 mb-3">🪪 {{ __('db.Identity Verification') }}</p>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="cnic_number" class="auth-label">{{ __('db.CNIC Number') }} <span class="text-red-400">*</span></label>
                                        <div class="auth-input-wrap">
                                            <i class="fa fa-id-card auth-icon"></i>
                                            <input id="cnic_number" type="text" name="cnic_number" value="{{ old('cnic_number') }}" required class="auth-input @error('cnic_number') auth-input-error @enderror" placeholder="XXXXX-XXXXXXX-X">
                                        </div>
                                        <x-input-error :messages="$errors->get('cnic_number')" class="mt-1" />
                                    </div>
                                    <div>
                                        <label class="auth-label">{{ __('db.Selfie Photo') }} <span class="text-red-400">*</span></label>
                                        <x-photo-upload-field name="selfie_photo" :required="true" :allow-gallery="false" />
                                        <p class="text-xs text-gray-400 mt-1">{{ __('db.Camera only — must be a live photo of you, not an uploaded file.') }}</p>
                                        <x-input-error :messages="$errors->get('selfie_photo')" class="mt-1" />
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">🔒 {{ __('db.Submitted directly and securely to Sallaamti — never shared, never collected any other way.') }}</p>
                            </div>

                            {{-- Payout details --}}
                            <div class="p-4 rounded-xl" style="background: var(--cream); border: 1px solid #e5e5e5">
                                <p class="text-sm font-semibold text-gray-700 mb-3">💳 {{ __('db.Commission Payout Details') }} <span class="text-gray-400 font-normal text-xs">{{ __("db.(required — this is where we'll send your commission once you're certified)") }}</span></p>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="payout_method" class="auth-label">{{ __('db.Payout Method') }} <span class="text-red-400">*</span></label>
                                        <select id="payout_method" name="payout_method" x-model="payoutMethod" required class="auth-input @error('payout_method') auth-input-error @enderror" style="padding-left: 1rem">
                                            <option value="">{{ __('db.Select') }}</option>
                                            <option value="bank_transfer" {{ old('payout_method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('db.Bank Transfer') }}</option>
                                            <option value="jazzcash" {{ old('payout_method') === 'jazzcash' ? 'selected' : '' }}>{{ __('db.JazzCash') }}</option>
                                            <option value="easypaisa" {{ old('payout_method') === 'easypaisa' ? 'selected' : '' }}>{{ __('db.EasyPaisa') }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('payout_method')" class="mt-1" />
                                    </div>
                                    <div x-show="payoutMethod" x-cloak>
                                        <label for="payout_account_title" class="auth-label">{{ __('db.Account Title') }} <span class="text-red-400">*</span></label>
                                        <input id="payout_account_title" type="text" name="payout_account_title" value="{{ old('payout_account_title') }}" :required="payoutMethod" class="auth-input @error('payout_account_title') auth-input-error @enderror" style="padding-left: 1rem">
                                        <x-input-error :messages="$errors->get('payout_account_title')" class="mt-1" />
                                    </div>
                                    <div x-show="payoutMethod" x-cloak>
                                        <label for="payout_account_number" class="auth-label">{{ __('db.Account / Mobile Number') }} <span class="text-red-400">*</span></label>
                                        <input id="payout_account_number" type="text" name="payout_account_number" value="{{ old('payout_account_number') }}" :required="payoutMethod" class="auth-input @error('payout_account_number') auth-input-error @enderror" style="padding-left: 1rem">
                                        <x-input-error :messages="$errors->get('payout_account_number')" class="mt-1" />
                                    </div>
                                    <div x-show="payoutMethod === 'bank_transfer'" x-cloak>
                                        <label for="payout_bank_name" class="auth-label">{{ __('db.Bank Name') }} <span class="text-red-400">*</span></label>
                                        <input id="payout_bank_name" type="text" name="payout_bank_name" value="{{ old('payout_bank_name') }}" :required="payoutMethod === 'bank_transfer'" class="auth-input @error('payout_bank_name') auth-input-error @enderror" style="padding-left: 1rem">
                                        <x-input-error :messages="$errors->get('payout_bank_name')" class="mt-1" />
                                    </div>
                                </div>
                            </div>

                            {{-- Consent + Terms (EN + UR) --}}
                            <div class="p-4 rounded-xl space-y-3" style="background: #fdfaf3; border: 1px solid #eee2c4">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" name="consent_accepted" id="consent_accepted" required class="auth-checkbox mt-0.5 flex-shrink-0">
                                    <label for="consent_accepted" class="text-sm text-gray-600 leading-relaxed">
                                        {{ __('db.I consent to Sallaamti verifying my identity documents and processing my information to evaluate this application.') }}
                                        <span class="block text-xs text-gray-500 mt-1" dir="rtl">میں سلامتی کو اپنی شناختی دستاویزات کی تصدیق اور اس درخواست کا جائزہ لینے کے لیے اپنی معلومات کے استعمال کی اجازت دیتا/دیتی ہوں۔</span>
                                    </label>
                                </div>
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" name="terms_accepted" id="terms_accepted" required class="auth-checkbox mt-0.5 flex-shrink-0">
                                    <label for="terms_accepted" class="text-sm text-gray-600 leading-relaxed">
                                        {!! __('db.I agree to the :codeOfConduct: I will never collect CNIC or documents over WhatsApp, never collect cash directly (all payments go through Sallaamti), never guarantee a match or make promises on Sallaamti\'s behalf, and will keep every client\'s information strictly confidential. Commission is single-level only — Sallaamti never pays for recruiting other counselors. A full signed agreement follows during onboarding.', ['codeOfConduct' => '<strong>' . __('db.Nikah Counselor Code of Conduct') . '</strong>']) !!}
                                        <span class="block text-xs text-gray-500 mt-1" dir="rtl">میں <strong>نکاح کونسلر ضابطہ اخلاق</strong> سے اتفاق کرتا/کرتی ہوں: میں کبھی واٹس ایپ پر شناختی کارڈ یا دستاویزات نہیں مانگوں گا/گی، کبھی نقد رقم خود وصول نہیں کروں گا/گی (تمام ادائیگیاں سلامتی کے ذریعے ہوں گی)، کبھی رشتہ طے ہونے کی ضمانت یا سلامتی کی طرف سے کوئی وعدہ نہیں کروں گا/گی، اور ہر کلائنٹ کی معلومات کو مکمل طور پر خفیہ رکھوں گا/گی۔ کمیشن صرف ایک سطح پر ہوگا — سلامتی کبھی کسی نئے کونسلر کو بھرتی کرنے پر کمیشن نہیں دیتا۔ مکمل دستخط شدہ معاہدہ تربیت کے دوران فراہم کیا جائے گا۔
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-base btn-teal w-full py-4 text-base font-bold">
                                {{ __('db.Submit My Application') }} <i class="fa fa-arrow-right ml-2"></i>
                            </button>

                            <p class="text-center text-xs text-gray-400">
                                {{ __('db.Our team will review your application and be in touch soon.') }}
                            </p>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">

                    <div class="rounded-2xl p-6 shadow-sm" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
                        <h5 class="font-bold text-white mb-1">{{ __('db.Why Become a Nikah Counselor?') }}</h5>
                        <p class="text-white/60 text-xs mb-4">آپ کیوں نکاح کونسلر بنیں؟</p>
                        <div class="space-y-3">
                            @foreach ([
                            ['🤲', __('db.Sadaqah Jariyah'), __('db.Help families find a halal spouse — a continuous reward, in this life and the next')],
                            ['💰', __('db.Earn Commission'), __('db.Get paid for every verified profile and Nikah counseling service you help deliver')],
                            ['📜', __('db.Official Certification'), __('db.A real Sallaamti Nikah Counselor ID, publicly verifiable — builds trust in your community')],
                            ['🌍', __('db.Build Your Standing'), __('db.Become the trusted representative for your area — respected work, not just a side hustle')],
                            ['📈', __('db.Grow With Us'), __('db.Certified counselors advance to Senior and Regional levels as they perform')],
                            ] as $w)
                            <div class="flex items-start gap-3">
                                <span class="text-xl flex-shrink-0">{{ $w[0] }}</span>
                                <div>
                                    <strong class="text-sm text-white">{{ $w[1] }}</strong>
                                    <p class="text-xs text-white/60 mb-0">{{ $w[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm" style="border-top: 3px solid #b8962e">
                        <h5 class="font-bold text-gray-800 mb-1">{{ __('db.How You Can Earn') }}</h5>
                        <p class="text-xs text-gray-400 mb-4">{{ __('db.Commission-based — actual earnings depend on verified customer activity, service sales, and performance. Not a fixed salary.') }}</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="text-gray-600">{{ __('db.Verified Profile') }}</span><span class="font-semibold" style="color: #0d6b6b">{{ __('db.Commission per profile') }}</span></div>
                            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="text-gray-600">{{ __('db.Assisted Nikah Counseling') }}</span><span class="font-semibold" style="color: #0d6b6b">{{ __('db.% of package') }}</span></div>
                            <div class="flex justify-between pb-1"><span class="text-gray-600">{{ __('db.Premium Nikah Counseling') }}</span><span class="font-semibold" style="color: #0d6b6b">{{ __('db.% of package') }}</span></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">{{ __("db.Exact rates are set by Sallaamti and shared during onboarding. Recognition bonuses exist for top performers — no MLM, ever: you're never paid for recruiting other counselors.") }}</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-3">{{ __('db.What Happens Next?') }}</h5>
                        <ol class="text-xs text-gray-500 space-y-2 list-decimal list-inside">
                            <li>{{ __('db.We review your application & documents') }}</li>
                            <li>{{ __('db.A short interview (call or in person)') }}</li>
                            <li>{{ __('db.Sign the Nikah Counselor Agreement') }}</li>
                            <li>{{ __('db.Complete training & a short assessment') }}</li>
                            <li>{{ __('db.Get certified — receive your ID, referral link & QR code') }}</li>
                        </ol>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-3">{{ __('db.Questions?') }}</h5>
                        <p class="text-gray-500 text-sm mb-4">{{ __('db.Reach out to us before applying if you have questions.') }}</p>
                        <a href="{{ whatsapp_link() }}" target="_blank" class="btn-base btn-teal w-full py-2.5 text-sm text-center block">
                            <i class="fab fa-whatsapp mr-2"></i>{{ __('db.WhatsApp Us') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
