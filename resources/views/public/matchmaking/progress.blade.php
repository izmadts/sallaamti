<x-guest-layout title="Your Matchmaking Progress — Sallaamti" description="Check the status of your matchmaking journey with Sallaamti.">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$unlocked)

            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4" style="background: var(--teal-light);">🔒</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Your Matchmaking Progress</h3>
                <p class="text-sm text-gray-500 mb-1" dir="rtl">آپ کی میچ میکنگ کی صورتحال</p>
                <p class="text-sm text-gray-500 mb-1">Enter the <strong>last 7 digits</strong> of the WhatsApp number your matchmaker has on file to view your status. You'll need to do this every time you visit this page — nothing is remembered on this device.</p>
                <p class="text-sm text-gray-500 mb-6" dir="rtl">اپنی صورتحال دیکھنے کے لیے اپنے میچ میکر کے پاس موجود واٹس ایپ نمبر کے <strong>آخری 7 ہندسے</strong> درج کریں۔ ہر بار اس صفحے پر آنے پر یہ دوبارہ کرنا ہوگا — اس ڈیوائس پر کچھ بھی یاد نہیں رکھا جاتا۔</p>

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
                    <input type="text" name="last7" inputmode="numeric" pattern="[0-9]{7}" maxlength="7" minlength="7" required placeholder="e.g. 3001234"
                        class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-48" autofocus>
                    <button class="text-white text-sm font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Unlock My Progress / میری صورتحال دیکھیں</button>
                </form>
            </div>

            @else

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">💍</span>
                <div>
                    <p class="text-sm text-gray-700">
                        Hello {{ $lead->name }} — here's everything your matchmaker has shared so far. This page always asks for verification again next time you visit.
                    </p>
                    <p class="text-sm text-gray-700 mt-1" dir="rtl">
                        السلام علیکم {{ $lead->name }} — یہاں وہ سب کچھ ہے جو آپ کے میچ میکر نے اب تک آپ کے ساتھ شیئر کیا ہے۔ اگلی بار آنے پر یہ صفحہ ہمیشہ دوبارہ تصدیق مانگے گا۔
                    </p>
                </div>
            </div>

            @if ($status ?? session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ $status ?? session('status') }}</div>
            @endif

            @php $pendingConsents = $lead->consentRequests->where('status', 'pending'); @endphp
            @if ($pendingConsents->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1">✅ Please Confirm</h4>
                <p class="text-xs text-gray-400 mb-1" dir="rtl">✅ براہ کرم تصدیق کریں</p>
                <p class="text-xs text-gray-500 mb-1">Your matchmaker is asking you to confirm the following. This goes straight to Sallaamti — nothing is assumed on your behalf.</p>
                <p class="text-xs text-gray-500 mb-4" dir="rtl">آپ کا میچ میکر آپ سے مندرجہ ذیل کی تصدیق مانگ رہا ہے۔ یہ براہ راست سلامتی کو جاتا ہے — آپ کی طرف سے کچھ بھی از خود فرض نہیں کیا جاتا۔</p>

                <div class="space-y-3">
                    @foreach ($pendingConsents as $req)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">{{ \App\Models\MatchmakingConsent::TYPES[$req->consent_type] }}</p>
                        <form method="POST" action="{{ route('public.matchmaking.progress.consents.respond', ['lead' => $lead->id, 'consentRequest' => $req->id, 't' => $lead->progress_link_token]) }}" class="flex flex-wrap gap-3">
                            @csrf
                            <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                            <button name="decision" value="grant" class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">✅ I Agree / میں راضی ہوں</button>
                            <button name="decision" value="decline" class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-5 py-2.5 rounded-lg transition">Not Now / ابھی نہیں</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($lead->nikahProfile && (empty($lead->nikahProfile->cnic_front_image) || empty($lead->nikahProfile->cnic_back_image) || empty($lead->nikahProfile->cnic_number)))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1">🪪 Upload Your Verification Documents</h4>
                <p class="text-xs text-gray-400 mb-1" dir="rtl">🪪 اپنی تصدیقی دستاویزات اپلوڈ کریں</p>
                <p class="text-xs text-gray-500 mb-1">Your matchmaker registered your profile but couldn't collect your CNIC/photo in person. Upload them here — this goes straight to Sallaamti for verification, never through your matchmaker's phone.</p>
                <p class="text-xs text-gray-500 mb-4" dir="rtl">آپ کے میچ میکر نے آپ کی پروفائل رجسٹر کر دی ہے لیکن آپ کا شناختی کارڈ/تصویر ذاتی طور پر جمع نہیں کر سکے۔ یہاں اپلوڈ کریں — یہ براہ راست سلامتی کو تصدیق کے لیے جاتا ہے، کبھی آپ کے میچ میکر کے فون سے نہیں۔</p>

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
                        <label class="text-xs text-gray-500 block mb-1">CNIC Number <span dir="rtl">/ شناختی کارڈ نمبر</span></label>
                        <input type="text" name="cnic_number" value="{{ old('cnic_number') }}" placeholder="e.g. 12345-1234567-1" class="border-gray-300 rounded-lg w-full text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">CNIC Photo (Front) <span dir="rtl">/ شناختی کارڈ کی تصویر (اگلا حصہ)</span></label>
                        <input type="file" name="cnic_front_image" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">CNIC Photo (Back) <span dir="rtl">/ شناختی کارڈ کی تصویر (پچھلا حصہ)</span></label>
                        <input type="file" name="cnic_back_image" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Your Photo (optional) <span dir="rtl">/ آپ کی تصویر (اختیاری)</span></label>
                        <input type="file" name="photo" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" class="rounded">
                        <label for="allow_photo_sharing" class="text-xs text-gray-600">
                            Allow my photo to be shared with a match after mutual interest is accepted
                            <span class="block" dir="rtl">باہمی رضامندی کے بعد میری تصویر رشتے کے ساتھ شیئر کرنے کی اجازت ہے</span>
                        </label>
                    </div>
                    <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Submit for Verification / تصدیق کے لیے جمع کروائیں</button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Current Status</p>
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

            {{-- Package + payment --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">📦 Matchmaking Package</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">📦 میچ میکنگ پیکج</p>

                @if ($lead->nikahPackage)
                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $lead->nikahPackage->name }} — Active</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if ($lead->package_expires_at) Valid until {{ $lead->package_expires_at->format('d M Y') }} @else No expiry @endif
                        </p>
                        <p class="text-xs text-gray-400" dir="rtl">
                            @if ($lead->package_expires_at) {{ $lead->package_expires_at->format('d M Y') }} تک درست @else کوئی معیاد ختم نہیں @endif
                        </p>
                    </div>
                    <span class="text-2xl">✅</span>
                </div>

                @elseif ($lead->package_payment_status === 'submitted')
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                    <p>⏳ Your {{ $lead->pendingPackage?->name }} package payment has been submitted and is awaiting confirmation by our team.</p>
                    <p dir="rtl" class="mt-1">⏳ آپ کے {{ $lead->pendingPackage?->name }} پیکج کی ادائیگی جمع کروا دی گئی ہے اور ہماری ٹیم کی تصدیق کی منتظر ہے۔</p>
                </div>

                @else
                @if ($lead->package_payment_status === 'rejected')
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm mb-4">
                    <p>❌ Your previous payment proof was rejected. Reason: {{ $lead->package_payment_rejection_reason }} Please choose a package and resubmit below.</p>
                    <p dir="rtl" class="mt-1">❌ آپ کی پچھلی ادائیگی مسترد کر دی گئی۔ وجہ: {{ $lead->package_payment_rejection_reason }} براہ کرم دوبارہ پیکج منتخب کر کے جمع کروائیں۔</p>
                </div>
                @endif

                <p class="text-sm text-gray-600 mb-1">Choose a package, then send the amount to the account below and upload your receipt.</p>
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
                        <p class="font-bold mb-1" style="color: #b8962e">📱 JazzCash</p>
                        <p class="text-gray-600 flex items-center gap-1">{{ setting('jazzcash_number') }} <x-copy-button :value="setting('jazzcash_number')" /></p>
                        <p class="font-semibold text-gray-700">{{ setting('jazzcash_account_title') }}</p>
                    </div>
                    @endif
                    @if (setting('easypaisa_number'))
                    <div>
                        <p class="font-bold mb-1" style="color: #b8962e">📱 EasyPaisa</p>
                        <p class="text-gray-600 flex items-center gap-1">{{ setting('easypaisa_number') }} <x-copy-button :value="setting('easypaisa_number')" /></p>
                    </div>
                    @endif
                    @if (setting('bank_name'))
                    <div>
                        <p class="font-bold mb-1" style="color: #b8962e">🏦 Bank Transfer</p>
                        <p class="text-gray-600">Bank: {{ setting('bank_name') }}</p>
                        <p class="text-gray-600">Account Title: {{ setting('bank_account_title') }}</p>
                        <p class="text-gray-600 flex items-center gap-1">Account No: {{ setting('bank_account_number') }} <x-copy-button :value="setting('bank_account_number')" /></p>
                        @if (setting('bank_account_iban'))
                        <p class="text-gray-600 flex items-center gap-1">IBAN: {{ setting('bank_account_iban') }} <x-copy-button :value="setting('bank_account_iban')" /></p>
                        @endif
                    </div>
                    @endif
                    @if (!setting('jazzcash_number') && !setting('easypaisa_number') && !setting('bank_name'))
                    <p class="text-red-600">Payment details have not been configured yet — contact your matchmaker before sending anything.</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('public.matchmaking.progress.package', ['lead' => $lead->id, 't' => $lead->progress_link_token]) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Package</label>
                        <select name="nikah_package_id" required class="border-gray-300 rounded-lg text-sm w-full">
                            <option value="">Select a package</option>
                            @foreach ($packages as $pkg)
                            <option value="{{ $pkg->id }}">{{ $pkg->name }} — Rs. {{ number_format($pkg->price) }} ({{ $pkg->duration_days ? $pkg->duration_days . ' days' : 'no expiry' }}@if($pkg->proposal_limit), {{ $pkg->proposal_limit }} proposals @endif)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Payment Method</label>
                        <select name="payment_method" required class="border-gray-300 rounded-lg text-sm w-full">
                            <option value="jazzcash">JazzCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="easypaisa">EasyPaisa</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Payment Reference (optional)</label>
                        <input type="text" name="payment_reference" class="border-gray-300 rounded-lg text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Payment Screenshot</label>
                        <input type="file" name="payment_screenshot" accept="image/*" capture="environment" required class="text-sm w-full">
                    </div>
                    <button class="w-full text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Submit Package Payment / پیکج ادائیگی جمع کروائیں</button>
                </form>
                @endif
            </div>

            {{-- Proposal / response history --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">💌 Proposals Shared With You</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">💌 آپ کے ساتھ شیئر کیے گئے رشتے</p>

                @forelse ($lead->proposalBatches->where('status', '!=', 'draft') as $batch)
                <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                    <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600">
                        Batch #{{ $batch->batch_number }} — {{ $batch->sent_at?->format('d M Y') }}
                    </div>
                    <div class="p-3 space-y-3">
                        @foreach ($batch->proposals as $proposal)
                        @if (!$proposal->response)
                        {{-- Pending — full detail card + response buttons, replaces what used to be a separate per-candidate link --}}
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="h-28 bg-gradient-to-br {{ $proposal->candidate->user?->gender === 'female' ? 'from-pink-100 to-rose-200' : 'from-blue-100 to-indigo-200' }} relative flex items-center justify-center">
                                <div class="w-20 h-20 rounded-full bg-white shadow flex items-center justify-center text-3xl border-4 border-white">
                                    {{ $proposal->candidate->user?->gender === 'female' ? '👩' : '👨' }}
                                </div>
                                <span class="absolute top-2 left-2 flex items-center gap-1 bg-green-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow">✅ Verified</span>
                            </div>
                            <div class="p-4">
                                <h5 class="font-bold text-gray-800 text-center">
                                    {{ $proposal->candidate->age }} yrs, {{ $proposal->candidate->city }}
                                    @if ($proposal->candidate->country && $proposal->candidate->country !== 'Pakistan') · {{ $proposal->candidate->country }} @endif
                                </h5>
                                <p class="text-gray-500 text-sm text-center mt-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $proposal->candidate->marital_status)) }}
                                    @if ($proposal->candidate->sect) · {{ $proposal->candidate->sect }} @endif
                                </p>

                                @if ($proposal->match_reasons)
                                <div class="mt-3 pt-3 border-t">
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Why your matchmaker suggested this / آپ کے میچ میکر نے یہ کیوں تجویز کیا</p>
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
                                    <div class="flex justify-between"><dt class="text-gray-400">Height</dt><dd class="text-gray-700">{{ $proposal->candidate->height }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->education)
                                    <div class="flex justify-between"><dt class="text-gray-400">Education</dt><dd class="text-gray-700">{{ $proposal->candidate->education }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->profession)
                                    <div class="flex justify-between"><dt class="text-gray-400">Profession</dt><dd class="text-gray-700">{{ $proposal->candidate->profession }}</dd></div>
                                    @endif
                                    @if ($proposal->candidate->family_type)
                                    <div class="flex justify-between"><dt class="text-gray-400">Family Type</dt><dd class="text-gray-700">{{ $proposal->candidate->family_type }}</dd></div>
                                    @endif
                                </dl>

                                @if ($proposal->candidate->about)
                                <p class="text-xs text-gray-600 leading-relaxed mt-3 pt-3 border-t">{{ $proposal->candidate->about }}</p>
                                @endif

                                <div class="mt-4 pt-4 border-t text-center">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">What do you think of this match?</p>
                                    <p class="text-xs text-gray-400 mb-3" dir="rtl">اس رشتے کے بارے میں آپ کی کیا رائے ہے؟</p>
                                    <form method="POST" action="{{ route('public.matchmaking.progress.proposals.respond', ['lead' => $lead->id, 'proposal' => $proposal->id, 't' => $lead->progress_link_token]) }}" class="flex flex-wrap justify-center gap-2">
                                        @csrf
                                        <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                                        <button name="response" value="interested" class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">👍 Interested / دلچسپی ہے</button>
                                        <button name="response" value="maybe" class="bg-amber-400 hover:bg-amber-500 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">🤔 Maybe / شاید</button>
                                        <button name="response" value="not_interested" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-lg transition">🙏 Not Interested / دلچسپی نہیں</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center justify-between text-sm border-b last:border-0 pb-2 last:pb-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ $proposal->candidate->age }} yrs, {{ $proposal->candidate->city }}</p>
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
                                {{ ucfirst(str_replace('_', ' ', $proposal->response)) }}
                            </span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-sm text-gray-400">
                    <p>No proposals shared yet — your matchmaker will send some here once they've found a match.</p>
                    <p class="mt-0.5" dir="rtl">ابھی تک کوئی رشتہ شیئر نہیں کیا گیا — جیسے ہی آپ کے میچ میکر کو کوئی مناسب رشتہ ملے گا وہ یہاں بھیج دیں گے۔</p>
                </div>
                @endforelse
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">🕓 Activity</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">🕓 سرگرمی</p>
                @forelse ($lead->timelineEvents as $event)
                <div class="py-2 border-b last:border-0">
                    <p class="text-sm text-gray-700">{{ $event->description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('d M Y') }}</p>
                </div>
                @empty
                <div class="text-sm text-gray-400">
                    <p>No activity yet.</p>
                    <p dir="rtl">ابھی تک کوئی سرگرمی نہیں۔</p>
                </div>
                @endforelse
            </div>

            @endif

        </div>
    </div>
</x-guest-layout>
