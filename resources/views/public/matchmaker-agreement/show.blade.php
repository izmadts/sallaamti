<x-guest-layout :title="__('db.Nikah Counselor Agreement — Sallaamti')" :description="__('db.Review and accept your Sallaamti Nikah Counselor Agreement and confidentiality agreement.')">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$unlocked)

            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4" style="background: var(--cream);">🔒</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ __('db.Nikah Counselor Agreement') }}</h3>
                <p class="text-sm text-gray-500 mb-1" dir="rtl">نکاح کاؤنسلر معاہدہ</p>
                <p class="text-sm text-gray-500 mb-1">{!! __('db.Hello :name — enter the :last7 of the mobile number on your application to review and accept your agreement.', ['name' => $application->full_name, 'last7' => '<strong>' . __('db.last 7 digits') . '</strong>']) !!}</p>
                <p class="text-sm text-gray-500 mb-6" dir="rtl">السلام علیکم {{ $application->full_name }} — اپنے معاہدے کو پڑھنے اور قبول کرنے کے لیے اپنی درخواست میں دیے گئے موبائل نمبر کے <strong>آخری 7 ہندسے</strong> درج کریں۔</p>

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
                    <button class="text-white text-sm font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">{{ __('db.Unlock Agreement') }} / معاہدہ کھولیں</button>
                </form>
            </div>

            @else

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">📜</span>
                <div>
                    <p class="text-sm text-gray-700">
                        {{ __('db.Hello :name — please read this carefully before accepting. This is a real agreement between you and Sallaamti.', ['name' => $application->full_name]) }}
                    </p>
                    <p class="text-sm text-gray-700 mt-1" dir="rtl">
                        السلام علیکم {{ $application->full_name }} — قبول کرنے سے پہلے یہ معاہدہ غور سے پڑھیں۔ یہ آپ اور سلامتی کے درمیان ایک حقیقی اور پابند معاہدہ ہے۔
                    </p>
                </div>
            </div>

            @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Plain-language summary --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">📋 {{ __('db.In Plain Words') }}</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">📋 آسان الفاظ میں</p>
                <ul class="text-sm text-gray-600 space-y-3 list-disc list-inside">
                    @foreach ([
                        [__("db.You represent Sallaamti — you introduce people and help them register, you don't personally decide or guarantee any match."), 'آپ سلامتی کی نمائندگی کرتے ہیں — آپ لوگوں کا تعارف کراتے اور رجسٹریشن میں مدد دیتے ہیں، رشتہ طے کرنے یا اس کی ضمانت دینے کا اختیار آپ کے پاس نہیں۔'],
                        [__("db.You're paid commission by Sallaamti according to its published rates — you never collect cash from a client yourself."), 'سلامتی آپ کو اپنی مقرر کردہ شرح کے مطابق کمیشن ادا کرتی ہے — آپ کبھی بھی کسی کلائنٹ سے خود نقد رقم وصول نہیں کرتے۔'],
                        [__("db.You never collect a client's CNIC or documents over WhatsApp — everything goes through Sallaamti's secure system."), 'آپ کبھی بھی کسی کلائنٹ کا شناختی کارڈ یا دستاویزات واٹس ایپ پر نہیں مانگتے — ہر چیز سلامتی کے محفوظ نظام کے ذریعے جاتی ہے۔'],
                        [__('db.Client information is confidential — no personal spreadsheets, no private database, no sharing outside Sallaamti.'), 'کلائنٹ کی معلومات خفیہ ہیں — کوئی ذاتی فہرست، نجی ڈیٹا بیس، یا سلامتی سے باہر شیئرنگ کی اجازت نہیں۔'],
                        [__('db.Every client you bring in belongs to Sallaamti, not to you personally — if you ever leave, Sallaamti keeps the relationship.'), 'آپ جو بھی کلائنٹ لاتے ہیں وہ سلامتی کی ملکیت ہوتا ہے، آپ کی ذاتی نہیں — اگر آپ کبھی چھوڑ دیں تو وہ تعلق سلامتی کے پاس ہی رہتا ہے۔'],
                        [__("db.You're never paid for recruiting other counselors — commission is only for your own verified work."), 'دوسرے کاؤنسلرز کو بھرتی کرنے پر آپ کو کبھی کمیشن نہیں ملتا — کمیشن صرف آپ کے اپنے تصدیق شدہ کام پر ملتا ہے۔'],
                        [__('db.This agreement can be ended by either side, and you must stop using the Sallaamti name and return/delete any client data immediately after.'), 'یہ معاہدہ کسی بھی فریق کی طرف سے ختم کیا جا سکتا ہے، اور ختم ہونے کے فوراً بعد آپ کو سلامتی کا نام استعمال کرنا بند کرنا اور تمام کلائنٹ ڈیٹا واپس/ڈیلیٹ کرنا ہوگا۔'],
                    ] as $point)
                    <li>
                        <p>{{ $point[0] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5" dir="rtl">{{ $point[1] }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Full agreement --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">{{ __('db.Nikah Counselor Agreement') }}</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">نکاح کاؤنسلر معاہدہ</p>
                <div class="text-sm text-gray-600 space-y-5 leading-relaxed">
                    @foreach ([
                        ['1. Role & Scope.', 'You act as an independent Sallaamti Nikah Counselor, introducing prospective clients to the Sallaamti platform, assisting them with registration and verification, and facilitating the Nikah counseling process described in Sallaamti\'s Nikah Counselor Code of Conduct. You do not have authority to make decisions on Sallaamti\'s behalf, guarantee any outcome, or bind Sallaamti to any promise.', '1. کردار اور دائرہ کار۔', 'آپ ایک آزاد سلامتی نکاح کاؤنسلر کے طور پر کام کرتے ہیں — نئے کلائنٹس کو سلامتی سے متعارف کراتے ہیں، ان کی رجسٹریشن اور تصدیق میں مدد دیتے ہیں، اور نکاح کاؤنسلر ضابطہ اخلاق میں بیان کردہ نکاح مشاورت کے عمل میں سہولت فراہم کرتے ہیں۔ آپ کو سلامتی کی طرف سے کوئی فیصلہ کرنے، کسی نتیجے کی ضمانت دینے، یا سلامتی کو کسی وعدے کا پابند کرنے کا اختیار حاصل نہیں۔'],
                        ['2. Commission & Payment.', 'You are compensated solely through commission on verified, Sallaamti-confirmed activity, at the rates published in your Sallaamti dashboard from time to time. Commission is calculated automatically, subject to a review hold period before approval, and paid to the bank/mobile-wallet account you provide. You will never collect payment directly from a client — all client payments must go through Sallaamti\'s own payment channels.', '2. کمیشن اور ادائیگی۔', 'آپ کو صرف تصدیق شدہ اور سلامتی سے منظور شدہ کام پر، آپ کے ڈیش بورڈ میں دی گئی شرح کے مطابق کمیشن ملتا ہے۔ کمیشن خودکار طریقے سے شمار ہوتا ہے، منظوری سے پہلے ایک مقررہ مدت تک رکا رہتا ہے، اور آپ کے دیے گئے بینک/موبائل والٹ اکاؤنٹ میں ادا کیا جاتا ہے۔ آپ کبھی بھی کسی کلائنٹ سے براہ راست رقم وصول نہیں کریں گے — ہر ادائیگی سلامتی کے اپنے نظام سے ہونی چاہیے۔'],
                        ['3. Confidentiality & Data Protection.', 'Any client information you access (names, contact details, documents, preferences, or any other personal data) is strictly confidential. You must not copy it into any personal record — no spreadsheet, notebook, phone contacts list, or third-party app — and must not disclose it to anyone outside Sallaamti except as required to perform your role.', '3. رازداری اور ڈیٹا کا تحفظ۔', 'آپ تک پہنچنے والی کسی بھی کلائنٹ کی معلومات (نام، رابطہ نمبر، دستاویزات، ترجیحات، یا کوئی اور ذاتی معلومات) مکمل طور پر خفیہ ہیں۔ آپ اسے کسی ذاتی ریکارڈ میں — کوئی فہرست، نوٹ بک، فون کانٹیکٹس، یا کسی تیسری ایپ میں — کاپی نہیں کر سکتے، اور اپنے کام کی ضرورت کے علاوہ سلامتی سے باہر کسی کو نہیں بتا سکتے۔'],
                        ['4. No Cash Collection.', 'You must never accept cash or any form of payment directly from a client on Sallaamti\'s behalf.', '4. نقد وصولی ممنوع۔', 'آپ کو سلامتی کی طرف سے کبھی بھی کسی کلائنٹ سے براہ راست نقد یا کسی بھی قسم کی ادائیگی وصول نہیں کرنی۔'],
                        ['5. No Guarantees.', 'You must never promise or guarantee a match, marriage, or any specific outcome, and must never make representations about a candidate\'s character, income, or family beyond what is stated in their verified Sallaamti profile.', '5. کسی ضمانت کی اجازت نہیں۔', 'آپ کبھی بھی رشتہ، نکاح، یا کسی نتیجے کا وعدہ یا ضمانت نہیں دے سکتے، اور کسی امیدوار کے کردار، آمدنی، یا خاندان کے بارے میں صرف وہی بات کر سکتے ہیں جو ان کی تصدیق شدہ سلامتی پروفائل میں درج ہو۔'],
                        ['6. Client Ownership.', 'Every client, lead, and profile you register or work with is and remains the property of Sallaamti, not your personal client. If this Agreement ends for any reason, all client relationships remain with Sallaamti and may be reassigned.', '6. کلائنٹ کی ملکیت۔', 'آپ جس بھی کلائنٹ، لیڈ، یا پروفائل کے ساتھ کام کرتے ہیں وہ ہمیشہ سلامتی کی ملکیت رہتا ہے، آپ کا ذاتی کلائنٹ نہیں۔ اگر یہ معاہدہ کسی بھی وجہ سے ختم ہو جائے، تو تمام کلائنٹ تعلقات سلامتی کے پاس رہیں گے اور کسی اور کو دیے جا سکتے ہیں۔'],
                        ['7. No Recruitment Commission.', 'You are compensated only for your own direct client-facing work. You will never receive commission, bonus, or any payment for recruiting, referring, or being connected to another counselor\'s activity.', '7. بھرتی پر کمیشن نہیں۔', 'آپ کو صرف اپنے براہ راست کلائنٹ کے کام پر معاوضہ ملتا ہے۔ کسی دوسرے کاؤنسلر کو بھرتی کرنے، حوالہ دینے، یا اس سے جڑے ہونے پر آپ کو کبھی کمیشن، بونس، یا کوئی ادائیگی نہیں ملے گی۔'],
                        ['8. Brand Use.', 'You may identify yourself as a "Sallaamti Nikah Counselor" using materials Sallaamti provides (your ID card, certificate, referral link/QR code). You may not create your own marketing materials using the Sallaamti name or logo without prior written approval.', '8. برانڈ کا استعمال۔', 'آپ اپنے آپ کو "سلامتی نکاح کاؤنسلر" کے طور پر متعارف کروا سکتے ہیں، صرف سلامتی کی فراہم کردہ چیزوں کے ذریعے (آپ کا آئی ڈی کارڈ، سرٹیفکیٹ، ریفرل لنک/QR کوڈ)۔ آپ سلامتی کے نام یا لوگو کے ساتھ اپنی مرضی سے مارکیٹنگ میٹریل بغیر پیشگی تحریری اجازت کے نہیں بنا سکتے۔'],
                        ['9. Conduct.', 'You must not discriminate against or harass any client, submit fake or misleading profiles, or advertise your services in a way that misrepresents Sallaamti or makes claims Sallaamti has not authorized.', '9. طرزِ عمل۔', 'آپ کسی بھی کلائنٹ کے ساتھ امتیازی سلوک یا ہراسانی نہیں کر سکتے، جعلی یا گمراہ کن پروفائل جمع نہیں کروا سکتے، اور اپنی خدمات کی تشہیر ایسے انداز میں نہیں کر سکتے جو سلامتی کو غلط طور پر پیش کرے یا ایسے دعوے کرے جن کی سلامتی نے اجازت نہ دی ہو۔'],
                        ['10. Termination.', 'Either you or Sallaamti may end this Agreement at any time, with or without cause. Upon termination, you must immediately stop representing yourself as a Sallaamti Nikah Counselor, return or permanently delete any client data or materials in your possession, and your access to Sallaamti systems will be revoked. Any commission already earned and approved before termination remains payable.', '10. معاہدے کا خاتمہ۔', 'آپ یا سلامتی، دونوں میں سے کوئی بھی، کسی بھی وقت اور کسی وجہ کے بغیر یہ معاہدہ ختم کر سکتا ہے۔ معاہدہ ختم ہونے پر آپ کو فوراً اپنے آپ کو سلامتی نکاح کاؤنسلر کہنا بند کرنا ہوگا، اپنے پاس موجود کلائنٹ ڈیٹا یا میٹریل واپس کرنا یا ڈیلیٹ کرنا ہوگا، اور سلامتی کے نظام تک آپ کی رسائی ختم کر دی جائے گی۔ ختم ہونے سے پہلے کمایا اور منظور شدہ کمیشن بہرحال ادا کیا جائے گا۔'],
                        ['11. Dispute Resolution.', 'Both parties will first attempt to resolve any disagreement in good faith directly. Unresolved disputes will be handled under the applicable laws of Pakistan.', '11. تنازعات کا حل۔', 'دونوں فریق پہلے کسی بھی اختلاف کو باہمی طور پر نیک نیتی سے حل کرنے کی کوشش کریں گے۔ اگر حل نہ ہو تو معاملہ پاکستان کے قابل اطلاق قوانین کے تحت دیکھا جائے گا۔'],
                        ['12. Intellectual Property.', 'The Sallaamti name, logo, certificate design, platform, and all related materials remain the property of Sallaamti at all times.', '12. دانشورانہ ملکیت۔', 'سلامتی کا نام، لوگو، سرٹیفکیٹ ڈیزائن، پلیٹ فارم، اور اس سے متعلق تمام مواد ہمیشہ سلامتی کی ملکیت رہیں گے۔'],
                    ] as $clause)
                    <div>
                        <p><strong>{{ $clause[0] }}</strong> {{ $clause[1] }}</p>
                        <p class="mt-1 text-gray-500" dir="rtl"><strong>{{ $clause[2] }}</strong> {{ $clause[3] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- NDA --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">{{ __('db.Confidentiality / NDA') }}</h4>
                <p class="text-xs text-gray-400 mb-3" dir="rtl">رازداری کا معاہدہ (NDA)</p>
                <div class="text-sm text-gray-600 space-y-5 leading-relaxed">
                    @foreach ([
                        ['1. Confidential Information.', 'This includes any client\'s personal, contact, financial, or identity information; Sallaamti\'s internal processes, commission structure, and business data; and any other non-public information you access through your role.', '1. خفیہ معلومات۔', 'اس میں شامل ہے: کسی بھی کلائنٹ کی ذاتی، رابطہ، مالی، یا شناختی معلومات؛ سلامتی کے اندرونی عمل، کمیشن کا ڈھانچہ، اور کاروباری ڈیٹا؛ اور کوئی بھی دوسری غیر عوامی معلومات جو آپ کو اپنے کام کے دوران ملے۔'],
                        ['2. No Independent Database.', 'You must not build, maintain, export, or copy Confidential Information into any system, device, or record outside Sallaamti\'s own platform.', '2. اپنا الگ ڈیٹا بیس نہیں۔', 'آپ خفیہ معلومات کو سلامتی کے اپنے پلیٹ فارم کے علاوہ کسی بھی سسٹم، ڈیوائس، یا ریکارڈ میں نہیں بنا سکتے، محفوظ نہیں کر سکتے، ایکسپورٹ یا کاپی نہیں کر سکتے۔'],
                        ['3. No Disclosure.', 'You must not share Confidential Information with anyone who does not have a legitimate need to know it as part of Sallaamti\'s own operations.', '3. کسی کو نہ بتانا۔', 'آپ خفیہ معلومات کسی ایسے شخص کو نہیں بتا سکتے جس کی سلامتی کے کام کے سلسلے میں اسے جاننے کی حقیقی ضرورت نہ ہو۔'],
                        ['4. Return or Deletion.', 'Upon request or upon termination of your Agreement, you must immediately return or permanently delete all Confidential Information in your possession, in any form.', '4. واپسی یا ڈیلیٹ کرنا۔', 'درخواست پر یا معاہدہ ختم ہونے پر، آپ کو اپنے پاس موجود تمام خفیہ معلومات، کسی بھی شکل میں، فوراً واپس کرنی یا مستقل طور پر ڈیلیٹ کرنی ہوں گی۔'],
                        ['5. Survival.', 'Your confidentiality obligations continue indefinitely after this Agreement ends.', '5. ہمیشہ کے لیے برقرار۔', 'آپ کی رازداری کی ذمہ داریاں اس معاہدے کے ختم ہونے کے بعد بھی ہمیشہ کے لیے برقرار رہتی ہیں۔'],
                    ] as $clause)
                    <div>
                        <p><strong>{{ $clause[0] }}</strong> {{ $clause[1] }}</p>
                        <p class="mt-1 text-gray-500" dir="rtl"><strong>{{ $clause[2] }}</strong> {{ $clause[3] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-400">{{ __('db.Sallaamti Nikah Counselor onboarding — full details available in the Nikah Counselor Code of Conduct.') }}</p>
                <p class="text-xs text-gray-400 mt-1" dir="rtl">سلامتی نکاح کاؤنسلر آن بورڈنگ — مکمل تفصیلات نکاح کاؤنسلر ضابطہ اخلاق میں دستیاب ہیں۔</p>
            </div>

            {{-- Accept --}}
            <div class="rounded-xl p-6" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
                <form method="POST" action="{{ $acceptUrl }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="agreement_accepted" value="1" required class="mt-0.5">
                        <span class="text-white text-sm">
                            {{ __('db.I have read and accept the Nikah Counselor Agreement above.') }}
                            <span class="block text-white/80 mt-0.5" dir="rtl">میں نے مذکورہ بالا نکاح کاؤنسلر معاہدہ پڑھ لیا ہے اور اسے قبول کرتا ہوں۔</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="nda_accepted" value="1" required class="mt-0.5">
                        <span class="text-white text-sm">
                            {{ __('db.I have read and accept the Confidentiality / NDA terms above.') }}
                            <span class="block text-white/80 mt-0.5" dir="rtl">میں نے مذکورہ بالا رازداری کے معاہدے (NDA) کی شرائط پڑھ لی ہیں اور انہیں قبول کرتا ہوں۔</span>
                        </span>
                    </label>

                    <button class="w-full bg-white text-gray-800 font-semibold py-3 rounded-lg hover:opacity-90 transition">{{ __('db.I Accept — Submit') }} / میں قبول کرتا ہوں — جمع کروائیں</button>
                </form>
            </div>

            @endif

        </div>
    </div>
</x-guest-layout>
