<x-guest-layout :title="__('db.Your Nikah Counseling Progress — Sallaamti')" :description="__('db.Check the status of your Nikah counseling journey with Sallaamti.')">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$unlocked)

            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4" style="background: var(--teal-light);">🔒</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ __('db.Your Nikah Counseling Progress') }}</h3>
                <p class="text-sm text-gray-500 mb-1" dir="rtl">آپ کی نکاح مشاورت کی صورتحال</p>
                <p class="text-sm text-gray-500 mb-1">{!! __('db.Enter the :last7 of the WhatsApp number your Nikah Counselor has on file to view your status. You\'ll need to do this every time you visit this page — nothing is remembered on this device.', ['last7' => '<strong>' . __('db.last 7 digits') . '</strong>']) !!}</p>
                <p class="text-sm text-gray-500 mb-6" dir="rtl">اپنی صورتحال دیکھنے کے لیے اپنے نکاح مشیر کے پاس موجود واٹس ایپ نمبر کے <strong>آخری 7 ہندسے</strong> درج کریں۔ ہر بار اس صفحے پر آنے پر یہ دوبارہ کرنا ہوگا — اس ڈیوائس پر کچھ بھی یاد نہیں رکھا جاتا۔</p>

                @if (($error ?? null) || $errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-left space-y-1">
                    <p>{{ $error ?? $errors->first() }}</p>
                    @if ($error_ur ?? null)
                    <p dir="rtl">{{ $error_ur }}</p>
                    @endif
                </div>
                @endif

                <form method="POST" action="{{ $verifyUrl }}" class="flex flex-col items-center gap-3">
                    @csrf
                    <input type="text" name="last7" inputmode="numeric" pattern="[0-9]{7}" maxlength="7" minlength="7" required placeholder="{{ __('db.e.g. 3001234') }}"
                        class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-48" autofocus>
                    <button class="text-white text-sm font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">{{ __('db.Unlock My Progress') }} / میری صورتحال دیکھیں</button>
                </form>
            </div>

            @else

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">💍</span>
                <div>
                    <p class="text-sm text-gray-700">
                        {{ __('db.Hello :name — here\'s what\'s next for you. This page always asks for verification again next time you visit.', ['name' => $lead->name]) }}
                    </p>
                    <p class="text-sm text-gray-700 mt-1" dir="rtl">
                        السلام علیکم {{ $lead->name }} — یہاں آپ کا اگلا مرحلہ ہے۔ اگلی بار آنے پر یہ صفحہ ہمیشہ دوبارہ تصدیق مانگے گا۔
                    </p>
                </div>
            </div>

            @if ($status ?? session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ $status ?? session('status') }}</div>
            @endif

            @if ($currentStep)
            {{-- One step at a time — see MatchmakingProgressController::buildActionQueue(). Whatever's next is the only card shown here; everything else waits below or stays in the collapsed history. --}}

                @switch($currentStep['type'])

                @case('consent')
                @php $req = $currentStep['data']; @endphp
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-gray-700 mb-1">✅ {{ __('db.One Thing to Confirm') }}</h4>
                    <p class="text-xs text-gray-400 mb-1" dir="rtl">✅ ایک چیز کی تصدیق درکار ہے</p>
                    <p class="text-xs text-gray-500 mb-4">{{ __('db.Your Nikah Counselor is asking you to confirm the following. This goes straight to Sallaamti — nothing is assumed on your behalf.') }}</p>
                    <p class="text-xs text-gray-500 mb-4" dir="rtl">آپ کا نکاح مشیر آپ سے مندرجہ ذیل کی تصدیق مانگ رہا ہے۔ یہ براہ راست سلامتی کو جاتا ہے — آپ کی طرف سے کچھ بھی از خود فرض نہیں کیا جاتا۔</p>

                    <div class="border border-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">{{ \App\Models\MatchmakingConsent::TYPES[$req->consent_type] }}</p>
                        <form method="POST" action="{{ route('public.matchmaking.progress.consents.respond', ['lead' => $lead->id, 'consentRequest' => $req->id, 't' => $lead->progress_link_token]) }}" class="flex flex-wrap gap-3">
                            @csrf
                            <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                            <button name="decision" value="grant" class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">✅ {{ __('db.I Agree') }} / میں راضی ہوں</button>
                            <button name="decision" value="decline" class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-5 py-2.5 rounded-lg transition">{{ __('db.Not Now') }} / ابھی نہیں</button>
                        </form>
                    </div>
                </div>
                @break

                @case('documents')
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-gray-700 mb-1">🪪 {{ __('db.Upload Your Verification Documents') }}</h4>
                    <p class="text-xs text-gray-400 mb-1" dir="rtl">🪪 اپنی تصدیقی دستاویزات اپلوڈ کریں</p>
                    <p class="text-xs text-gray-500 mb-1">{{ __('db.Your Nikah Counselor registered your profile but couldn\'t collect your CNIC/photo in person. Upload them here — this goes straight to Sallaamti for verification, never through your Nikah Counselor\'s phone.') }}</p>
                    <p class="text-xs text-gray-500 mb-4" dir="rtl">آپ کے نکاح مشیر نے آپ کی پروفائل رجسٹر کر دی ہے لیکن آپ کا شناختی کارڈ/تصویر ذاتی طور پر جمع نہیں کر سکے۔ یہاں اپلوڈ کریں — یہ براہ راست سلامتی کو تصدیق کے لیے جاتا ہے، کبھی آپ کے نکاح مشیر کے فون سے نہیں۔</p>

                    @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ $documentsUrl }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">

                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.CNIC Number') }} <span dir="rtl">/ شناختی کارڈ نمبر</span></label>
                            <input type="text" name="cnic_number" value="{{ old('cnic_number') }}" placeholder="{{ __('db.e.g. 12345-1234567-1') }}" class="border-gray-300 rounded-lg w-full text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.CNIC Photo (Front)') }} <span dir="rtl">/ شناختی کارڈ کی تصویر (اگلا حصہ)</span></label>
                            <x-photo-upload-field name="cnic_front_image" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.CNIC Photo (Back)') }} <span dir="rtl">/ شناختی کارڈ کی تصویر (پچھلا حصہ)</span></label>
                            <x-photo-upload-field name="cnic_back_image" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.Your Photo (optional)') }} <span dir="rtl">/ آپ کی تصویر (اختیاری)</span></label>
                            <x-photo-upload-field name="photo" />
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" class="rounded">
                            <label for="allow_photo_sharing" class="text-xs text-gray-600">
                                {{ __('db.Allow my photo to be shared with a match after mutual interest is accepted') }}
                                <span class="block" dir="rtl">باہمی رضامندی کے بعد میری تصویر رشتے کے ساتھ شیئر کرنے کی اجازت ہے</span>
                            </label>
                        </div>
                        <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">{{ __('db.Submit for Verification') }} / تصدیق کے لیے جمع کروائیں</button>
                    </form>
                </div>
                @break

                @case('package')
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-gray-700 mb-1">📦 {{ __('db.Choose Your Package') }}</h4>
                    <p class="text-xs text-gray-400 mb-3" dir="rtl">📦 اپنا پیکج منتخب کریں</p>

                    @if ($lead->package_payment_status === 'rejected')
                    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm mb-4">
                        <p>❌ {{ __('db.Your previous payment proof was rejected. Reason: :reason Please choose a package and resubmit below.', ['reason' => $lead->package_payment_rejection_reason]) }}</p>
                        <p dir="rtl" class="mt-1">❌ آپ کی پچھلی ادائیگی مسترد کر دی گئی۔ وجہ: {{ $lead->package_payment_rejection_reason }} براہ کرم دوبارہ پیکج منتخب کر کے جمع کروائیں۔</p>
                    </div>
                    @endif

                    <p class="text-sm text-gray-600 mb-1">{{ __('db.Choose a package, then send the amount to the account below and upload your receipt.') }}</p>
                    <p class="text-xs text-gray-400 mb-4" dir="rtl">پیکج منتخب کریں، پھر نیچے دیے گئے اکاؤنٹ میں رقم بھیجیں اور رسید اپلوڈ کریں۔</p>

                    @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm mb-4 space-y-4">
                        @if (setting('jazzcash_number'))
                        <div>
                            <p class="font-bold mb-1" style="color: #b8962e">📱 {{ __('db.JazzCash') }}</p>
                            <p class="text-gray-600 flex items-center gap-1">{{ setting('jazzcash_number') }} <x-copy-button :value="setting('jazzcash_number')" /></p>
                            <p class="font-semibold text-gray-700">{{ setting('jazzcash_account_title') }}</p>
                        </div>
                        @endif
                        @if (setting('easypaisa_number'))
                        <div>
                            <p class="font-bold mb-1" style="color: #b8962e">📱 {{ __('db.EasyPaisa') }}</p>
                            <p class="text-gray-600 flex items-center gap-1">{{ setting('easypaisa_number') }} <x-copy-button :value="setting('easypaisa_number')" /></p>
                        </div>
                        @endif
                        @if (setting('bank_name'))
                        <div>
                            <p class="font-bold mb-1" style="color: #b8962e">🏦 {{ __('db.Bank Transfer') }}</p>
                            <p class="text-gray-600">{{ __('db.Bank:') }} {{ setting('bank_name') }}</p>
                            <p class="text-gray-600">{{ __('db.Account Title:') }} {{ setting('bank_account_title') }}</p>
                            <p class="text-gray-600 flex items-center gap-1">{{ __('db.Account No:') }} {{ setting('bank_account_number') }} <x-copy-button :value="setting('bank_account_number')" /></p>
                            @if (setting('bank_account_iban'))
                            <p class="text-gray-600 flex items-center gap-1">{{ __('db.IBAN:') }} {{ setting('bank_account_iban') }} <x-copy-button :value="setting('bank_account_iban')" /></p>
                            @endif
                        </div>
                        @endif
                        @if (!setting('jazzcash_number') && !setting('easypaisa_number') && !setting('bank_name'))
                        <p class="text-red-600">{{ __('db.Payment details have not been configured yet — contact your Nikah Counselor before sending anything.') }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('public.matchmaking.progress.package', ['lead' => $lead->id, 't' => $lead->progress_link_token]) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.Package') }}</label>
                            <select name="nikah_package_id" required class="border-gray-300 rounded-lg text-sm w-full">
                                <option value="">{{ __('db.Select a package') }}</option>
                                @foreach ($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->name }} — Rs. {{ number_format($pkg->price) }} ({{ $pkg->duration_days ? __('db.:days days', ['days' => $pkg->duration_days]) : __('db.no expiry') }}@if($pkg->proposal_limit), {{ __('db.:count proposals', ['count' => $pkg->proposal_limit]) }} @endif)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.Payment Method') }}</label>
                            <select name="payment_method" required class="border-gray-300 rounded-lg text-sm w-full">
                                <option value="jazzcash">{{ __('db.JazzCash') }}</option>
                                <option value="bank_transfer">{{ __('db.Bank Transfer') }}</option>
                                <option value="easypaisa">{{ __('db.EasyPaisa') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.Payment Reference (optional)') }}</label>
                            <input type="text" name="payment_reference" class="border-gray-300 rounded-lg text-sm w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1">{{ __('db.Payment Screenshot') }}</label>
                            <x-photo-upload-field name="payment_screenshot" :required="true" :allow-camera="false" />
                        </div>
                        <button class="w-full text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">{{ __('db.Submit Package Payment') }} / پیکج ادائیگی جمع کروائیں</button>
                    </form>
                </div>
                @break

                @case('package_pending')
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center text-2xl mb-3 bg-amber-50">⏳</div>
                    <h4 class="font-semibold text-gray-700 mb-1">{{ __('db.Package Payment Under Review') }}</h4>
                    <p class="text-xs text-gray-400 mb-3" dir="rtl">پیکج کی ادائیگی زیرِ جائزہ ہے</p>
                    <p class="text-sm text-gray-600">{{ __('db.Your :package package payment has been submitted and is awaiting confirmation by our team.', ['package' => $lead->pendingPackage?->name]) }}</p>
                    <p class="text-sm text-gray-600 mt-1" dir="rtl">آپ کے {{ $lead->pendingPackage?->name }} پیکج کی ادائیگی جمع کروا دی گئی ہے اور ہماری ٹیم کی تصدیق کی منتظر ہے۔</p>
                </div>
                @break

                @case('account_setup')
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="text-center mb-4">
                        <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center text-2xl mb-3" style="background: var(--teal-light, #e0f2f1)">🔐</div>
                        <h4 class="font-semibold text-gray-700 mb-1">{{ __('db.Set Up Quick Access') }}</h4>
                        <p class="text-xs text-gray-400 mb-3" dir="rtl">🔐 فوری رسائی ترتیب دیں</p>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">{{ __('db.Optional — create a 4-digit PIN so you can check your status anytime at sallaamti.com, not just this link. You\'ll still be able to use everything on this page either way.') }}</p>
                    <p class="text-sm text-gray-600 mb-4" dir="rtl">اختیاری — ایک 4 ہندسوں کا PIN بنائیں تاکہ آپ کبھی بھی sallaamti.com پر اپنی صورتحال دیکھ سکیں، نہ صرف اس لنک سے۔ آپ اس صفحے کی ہر سہولت بہرحال استعمال کر سکیں گے۔</p>

                    @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('public.matchmaking.progress.account.register', ['lead' => $lead->id, 't' => $lead->progress_link_token]) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                        <div class="flex justify-center gap-3 flex-wrap">
                            <div>
                                <label class="text-xs text-gray-500 block mb-1 text-center">{{ __('db.4-Digit PIN') }}</label>
                                <input type="text" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required
                                    class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-32">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block mb-1 text-center">{{ __('db.Confirm PIN') }}</label>
                                <input type="text" name="pin_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required
                                    class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-32">
                            </div>
                        </div>
                        <button class="w-full text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">{{ __('db.Set Up My PIN') }} / میرا PIN ترتیب دیں</button>
                    </form>
                    <form method="POST" action="{{ route('public.matchmaking.progress.account.skip', ['lead' => $lead->id, 't' => $lead->progress_link_token]) }}" class="mt-2 text-center">
                        @csrf
                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                        <button class="text-xs text-gray-400 hover:text-gray-600 underline">{{ __('db.Not now') }} / ابھی نہیں</button>
                    </form>
                </div>
                @break

                @case('proposal_batch')
                @php $batch = $currentStep['data']; $pendingProposals = $currentStep['pending']; @endphp
                <div>
                    <div class="text-center mb-4">
                        <h4 class="font-semibold text-gray-700">💌 {{ $pendingProposals->count() > 1 ? __('db.Proposed Matches For You') : __('db.A Proposed Match For You') }}</h4>
                        <p class="text-xs text-gray-400" dir="rtl">💌 آپ کے لیے تجویز کردہ {{ $pendingProposals->count() > 1 ? 'رشتے' : 'رشتہ' }}</p>
                    </div>
                    <div class="space-y-4">
                        @foreach ($pendingProposals as $proposal)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="h-28 bg-gradient-to-br {{ $proposal->candidate->user?->gender === 'female' ? 'from-pink-100 to-rose-200' : 'from-blue-100 to-indigo-200' }} relative flex items-center justify-center">
                                <div class="w-20 h-20 rounded-full bg-white shadow flex items-center justify-center text-3xl border-4 border-white">
                                    {{ $proposal->candidate->user?->gender === 'female' ? '👩' : '👨' }}
                                </div>
                                <span class="absolute top-2 left-2 flex items-center gap-1 bg-green-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow">✅ {{ __('db.Verified') }}</span>
                            </div>
                            <div class="p-4">
                                <h5 class="font-bold text-gray-800 text-center">
                                    {{ __('db.:age yrs, :city', ['age' => $proposal->candidate->age, 'city' => $proposal->candidate->city]) }}
                                    @if ($proposal->candidate->country && $proposal->candidate->country !== 'Pakistan') · {{ $proposal->candidate->country }} @endif
                                </h5>
                                <p class="text-gray-500 text-sm text-center mt-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $proposal->candidate->marital_status)) }}
                                    @if ($proposal->candidate->sect) · {{ $proposal->candidate->sect }} @endif
                                </p>

                                @if ($proposal->match_reasons)
                                <div class="mt-3 pt-3 border-t">
                                    <p class="text-xs font-semibold text-gray-500 mb-1">{{ __('db.Why your Nikah Counselor suggested this') }} / آپ کے نکاح مشیر نے یہ کیوں تجویز کیا</p>
                                    <ul class="text-xs text-gray-600 space-y-0.5 list-disc list-inside">
                                        @foreach ($proposal->match_reasons as $reason)
                                        @if (trim($reason) !== '')
                                        <li>{{ $reason }}</li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <dl class="text-xs mt-3 pt-3 border-t space-y-1">
                                    @if ($proposal->candidate->height)
                                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Height') }}</dt><dd class="text-gray-700">{{ $proposal->candidate->height }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->education)
                                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Education') }}</dt><dd class="text-gray-700">{{ $proposal->candidate->education }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->profession)
                                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Profession') }}</dt><dd class="text-gray-700">{{ $proposal->candidate->profession }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->family_type)
                                    <div class="flex justify-between"><dt class="text-gray-400">{{ __('db.Family Type') }}</dt><dd class="text-gray-700">{{ $proposal->candidate->family_type }}</dd></div>
                                    @endif
                                </dl>

                                @if ($proposal->candidate->about)
                                <p class="text-xs text-gray-600 leading-relaxed mt-3 pt-3 border-t">{{ $proposal->candidate->about }}</p>
                                @endif

                                <div class="mt-4 pt-4 border-t text-center">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">{{ __('db.What do you think of this match?') }}</p>
                                    <p class="text-xs text-gray-400 mb-3" dir="rtl">اس رشتے کے بارے میں آپ کی کیا رائے ہے؟</p>
                                    <form method="POST" action="{{ route('public.matchmaking.progress.proposals.respond', ['lead' => $lead->id, 'proposal' => $proposal->id, 't' => $lead->progress_link_token]) }}" class="flex flex-wrap justify-center gap-2">
                                        @csrf
                                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                                        <button name="response" value="interested" class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">👍 {{ __('db.Interested') }} / دلچسپی ہے</button>
                                        <button name="response" value="maybe" class="bg-amber-400 hover:bg-amber-500 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">🤔 {{ __('db.Maybe') }} / شاید</button>
                                        <button name="response" value="not_interested" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-lg transition">🙏 {{ __('db.Not Interested') }} / دلچسپی نہیں</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @break

                @endswitch

                @if ($stepsRemaining > 0)
                <p class="text-center text-xs text-gray-400">+{{ $stepsRemaining }} {{ $stepsRemaining > 1 ? __('db.more things waiting after this') : __('db.more thing waiting after this') }} / اس کے بعد {{ $stepsRemaining }} اور چیز باقی ہے</p>
                @endif

            @else
            {{-- Nothing waiting on the client right now — no CNIC/consent/package/proposal to act on --}}
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="text-4xl mb-2">🎉</div>
                <h4 class="font-semibold text-gray-700 mb-1">{{ __("db.You're All Caught Up") }}</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">آپ کا سب کچھ مکمل ہے</p>
                <p class="text-sm text-gray-600">{{ __("db.Nothing needs your attention right now — your Nikah Counselor will reach out here as soon as there's something new.") }}</p>
                <p class="text-sm text-gray-600 mt-1" dir="rtl">فی الحال آپ کی توجہ درکار کسی چیز کی ضرورت نہیں — جیسے ہی کوئی نئی بات ہوگی آپ کا نکاح مشیر یہیں رابطہ کرے گا۔</p>
            </div>
            @endif

            {{-- Everything else — status, package, full proposal history, activity — stays available, just out of the way of the one thing that actually needs a response right now. --}}
            <details class="bg-white rounded-xl shadow-sm">
                <summary class="p-6 font-semibold text-gray-700 cursor-pointer">📜 {{ __('db.Your Full Status & History') }} <span class="text-xs text-gray-400 font-normal" dir="rtl">/ آپ کی مکمل صورتحال اور تاریخ</span></summary>
                <div class="px-6 pb-6 space-y-6">

                    <div class="text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">{{ __('db.Current Status') }}</p>
                        <p class="text-xs text-gray-400 mb-2" dir="rtl">موجودہ صورتحال</p>
                        <span class="inline-block text-sm px-3 py-1 rounded-full font-semibold
                            {{ match($lead->status) {
                                'new' => 'bg-blue-100 text-blue-800',
                                'contacted' => 'bg-amber-100 text-amber-800',
                                'interested' => 'bg-purple-100 text-purple-800',
                                'registered' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                        </span>
                    </div>

                    @if ($lead->nikahPackage)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('db.Package') }}</p>
                        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $lead->nikahPackage->name }} — {{ __('db.Active') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    @if ($lead->package_expires_at) {{ __('db.Valid until :date', ['date' => $lead->package_expires_at->format('d M Y')]) }} @else {{ __('db.No expiry') }} @endif
                                </p>
                            </div>
                            <span class="text-2xl">✅</span>
                        </div>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('db.Proposals Shared With You') }}</p>
                        @forelse ($lead->proposalBatches->where('status', '!=', 'draft') as $batch)
                        <div class="mb-3 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600">
                                {{ __('db.Batch #:number', ['number' => $batch->batch_number]) }} — {{ $batch->sent_at?->format('d M Y') }}
                            </div>
                            <div class="p-3 space-y-2">
                                @foreach ($batch->proposals as $proposal)
                                <div class="flex items-center justify-between text-sm border-b last:border-0 pb-2 last:pb-0">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ __('db.:age yrs, :city', ['age' => $proposal->candidate->age, 'city' => $proposal->candidate->city]) }}</p>
                                        @if ($proposal->candidate->sect)
                                        <p class="text-xs text-gray-400">{{ $proposal->candidate->sect }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ match($proposal->response) {
                                            'interested' => 'bg-green-100 text-green-800',
                                            'not_interested' => 'bg-red-100 text-red-700',
                                            'maybe' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ $proposal->response ? ucfirst(str_replace('_', ' ', $proposal->response)) : __('db.Awaiting your response') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">{{ __("db.No proposals shared yet — your Nikah Counselor will send some here once they've found a match.") }}</p>
                        @endforelse
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('db.Activity') }}</p>
                        @forelse ($lead->timelineEvents as $event)
                        <div class="py-2 border-b last:border-0">
                            <p class="text-sm text-gray-700">{{ $event->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('d M Y') }}</p>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">{{ __('db.No activity yet.') }}</p>
                        @endforelse
                    </div>

                </div>
            </details>

            @endif

        </div>
    </div>
</x-guest-layout>
