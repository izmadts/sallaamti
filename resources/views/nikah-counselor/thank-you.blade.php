<x-guest-layout :title="__('db.Application Received — Sallaamti Nikah Counselor')" :description="__('db.Your Nikah Counselor application has been received. Here\'s what happens next, your role and duties, and the Do\'s and Don\'ts every counselor follows.')">

    {{-- Page Hero --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 overflow-hidden" style="font-size: 18rem; color: #fff; display: flex; align-items: center; justify-content: center; pointer-events: none;">✅</div>
        <div class="max-w-4xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <div class="text-5xl mb-3">✅</div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">{{ __('db.Application Received!') }}</h1>
            <p class="text-white text-2xl font-bold mb-3" dir="rtl">درخواست موصول ہو گئی!</p>
            <p class="text-white/70 max-w-2xl mx-auto">{{ __('db.Thank you for applying to become a Sallaamti Nikah Counselor. Please read this page carefully — it explains what happens next, your role, and the rules every counselor follows.') }}</p>
            <p class="text-white/70 max-w-2xl mx-auto mt-2" dir="rtl">سلامتی نکاح کاؤنسلر بننے کے لیے درخواست دینے کا شکریہ۔ براہ کرم یہ صفحہ غور سے پڑھیں — اس میں اگلے مراحل، آپ کا کردار، اور ہر کاؤنسلر کے لیے ضروری اصول بتائے گئے ہیں۔</p>
        </div>
    </section>

    <section class="py-14 bg-cream">
        <div class="max-w-4xl mx-auto px-4 space-y-6">

            {{-- What Happens Next --}}
            <div class="bg-white rounded-2xl shadow-sm p-8" style="border-top: 4px solid #0d6b6b">
                <h3 class="font-bold text-xl text-gray-800 mb-1">📋 {{ __('db.What Happens Next — Approval Process') }}</h3>
                <p class="text-sm text-gray-500 mb-1" dir="rtl">اگلے مراحل — منظوری کا عمل</p>
                <p class="text-sm text-gray-500 mb-6">{{ __("db.Every application goes through the same stages, in order. Our team moves you forward at each step — you don't need to do anything except respond when we reach out.") }}</p>

                <div class="space-y-0">
                    @foreach (\App\Models\MatchmakerApplication::STEPS as $key => $label)
                    @php $isLast = $loop->last; @endphp
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background: {{ $key === 'certified' ? '#b8962e' : '#0d6b6b' }}">{{ $loop->iteration }}</div>
                            @unless ($isLast)
                            <div class="w-0.5 flex-1 my-1" style="background: #e5e5e5; min-height: 24px"></div>
                            @endunless
                        </div>
                        <div class="pb-6">
                            <p class="font-semibold text-gray-800">{{ $label }}</p>
                            <p class="text-sm text-gray-500" dir="rtl">{{ \App\Models\MatchmakerApplication::STEPS_UR[$key] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-2 p-4 rounded-xl text-sm" style="background: var(--cream); border: 1px solid #e5e5e5">
                    <p class="text-gray-600">{{ __("db.You'll receive your Agreement & NDA link to sign, then — once fully certified — your official Sallaamti Nikah Counselor ID, referral link, and QR code, and you'll be able to log in and start working.") }}</p>
                    <p class="text-gray-600 mt-2" dir="rtl">آپ کو معاہدہ اور رازداری کا معاہدہ دستخط کے لیے بھیجا جائے گا، اور مکمل سرٹیفیکیشن کے بعد آپ کو اپنا سرکاری سلامتی نکاح کاؤنسلر آئی ڈی، ریفرل لنک، اور QR کوڈ ملے گا — پھر آپ لاگ ان کر کے کام شروع کر سکیں گے۔</p>
                </div>
            </div>

            {{-- Your Role --}}
            <div class="bg-white rounded-2xl shadow-sm p-8" style="border-top: 4px solid #b8962e">
                <h3 class="font-bold text-xl text-gray-800 mb-1">💍 {{ __('db.Your Role as a Nikah Counselor') }}</h3>
                <p class="text-sm text-gray-500 mb-6" dir="rtl">نکاح کاؤنسلر کے طور پر آپ کا کردار</p>

                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach ([
                        ['🗂️', __('db.Help genuine, serious people register a real, verified Nikah profile with Sallaamti.'), 'مخلص اور سنجیدہ لوگوں کو سلامتی پر ایک حقیقی، تصدیق شدہ نکاح پروفائل بنانے میں مدد کریں۔'],
                        ['💼', __('db.Guide clients through Nikah counseling packages and help them find suitable proposals.'), 'کلائنٹس کو نکاح مشاورت پیکجز میں رہنمائی دیں اور مناسب رشتے تلاش کرنے میں مدد کریں۔'],
                        ['🤝', __('db.Facilitate introductions — you suggest and connect, you never decide or guarantee an outcome for anyone.'), 'تعارف کرائیں — آپ تجویز اور رابطہ کراتے ہیں، کبھی کسی کے لیے فیصلہ یا نتیجے کی ضمانت نہیں دیتے۔'],
                        ['🌍', __('db.Represent Sallaamti in your community with trust — you are the face people rely on locally.'), 'اپنی کمیونٹی میں سلامتی کی نمائندگی اعتماد کے ساتھ کریں — لوگ مقامی طور پر آپ پر بھروسہ کرتے ہیں۔'],
                    ] as $duty)
                    <div class="flex items-start gap-3 p-4 rounded-xl" style="background: var(--cream)">
                        <span class="text-2xl flex-shrink-0">{{ $duty[0] }}</span>
                        <div>
                            <p class="text-sm text-gray-700">{{ $duty[1] }}</p>
                            <p class="text-xs text-gray-500 mt-1" dir="rtl">{{ $duty[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Do's and Don'ts --}}
            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Do's --}}
                <div class="bg-white rounded-2xl shadow-sm p-6" style="border-top: 4px solid #16a34a">
                    <h3 class="font-bold text-lg text-gray-800 mb-1">✅ {{ __("db.Do's") }}</h3>
                    <p class="text-xs text-gray-500 mb-4" dir="rtl">کیا کرنا ہے</p>
                    <ul class="space-y-3">
                        @foreach ([
                            [__("db.Always send CNIC and documents only through Sallaamti's official system."), 'شناختی کارڈ اور دستاویزات ہمیشہ صرف سلامتی کے سرکاری نظام کے ذریعے بھیجیں۔'],
                            [__("db.Always route every payment through Sallaamti's official channels only."), 'ہر ادائیگی ہمیشہ صرف سلامتی کے سرکاری ذرائع سے کروائیں۔'],
                            [__('db.Use the word "verified" precisely — it means Sallaamti\'s process was completed, nothing more.'), '"تصدیق شدہ" کا لفظ درست معنوں میں استعمال کریں — اس کا مطلب صرف یہ ہے کہ سلامتی کا عمل مکمل ہو چکا ہے۔'],
                            [__("db.Keep every client's information strictly confidential, always."), 'ہر کلائنٹ کی معلومات ہمیشہ مکمل طور پر خفیہ رکھیں۔'],
                            [__('db.Prefer gender-matched Nikah counseling where it helps comfort and privacy.'), 'جہاں آسانی اور رازداری کے لیے مفید ہو، ہم جنس نکاح مشاورت کو ترجیح دیں۔'],
                            [__("db.Represent Sallaamti's mission — help families, don't chase quick income."), 'سلامتی کے مشن کی نمائندگی کریں — خاندانوں کی مدد کریں، جلد بازی میں آمدنی کے پیچھے نہ بھاگیں۔'],
                        ] as $item)
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 flex-shrink-0 mt-0.5">✓</span>
                            <div>
                                <p class="text-sm text-gray-700">{{ $item[0] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5" dir="rtl">{{ $item[1] }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Don'ts --}}
                <div class="bg-white rounded-2xl shadow-sm p-6" style="border-top: 4px solid #dc2626">
                    <h3 class="font-bold text-lg text-gray-800 mb-1">🚫 {{ __("db.Don'ts") }}</h3>
                    <p class="text-xs text-gray-500 mb-4" dir="rtl">کیا نہیں کرنا</p>
                    <ul class="space-y-3">
                        @foreach ([
                            [__('db.Never collect CNIC or documents over WhatsApp — no exceptions.'), 'واٹس ایپ پر کبھی شناختی کارڈ یا دستاویزات نہ مانگیں — کوئی استثناء نہیں۔'],
                            [__('db.Never collect cash directly from a client, ever.'), 'کبھی بھی کسی کلائنٹ سے براہ راست نقد رقم وصول نہ کریں۔'],
                            [__('db.Never guarantee a match, income, character, or any outcome.'), 'کبھی بھی رشتہ طے ہونے، آمدنی، کردار، یا کسی نتیجے کی ضمانت نہ دیں۔'],
                            [__('db.Never keep a private database (Excel, Google Sheets, personal CRM) — it belongs to Sallaamti.'), 'کبھی نجی ڈیٹا بیس (ایکسل، گوگل شیٹس، ذاتی سی آر ایم) نہ رکھیں — یہ سلامتی کی ملکیت ہے۔'],
                            [__('db.Never recruit other counselors for commission — single-level only, no MLM, ever.'), 'کبھی کمیشن کے لیے دوسرے کاؤنسلرز کو بھرتی نہ کریں — صرف ایک سطح، کبھی MLM نہیں۔'],
                            [__('db.Never advertise with unauthorized or aggressive income claims.'), 'کبھی غیر مجاز یا مبالغہ آمیز آمدنی کے دعووں کے ساتھ اشتہار نہ دیں۔'],
                        ] as $item)
                        <li class="flex items-start gap-2">
                            <span class="text-red-600 flex-shrink-0 mt-0.5">✕</span>
                            <div>
                                <p class="text-sm text-gray-700">{{ $item[0] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5" dir="rtl">{{ $item[1] }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <p class="text-sm text-gray-600 mb-1">{{ __('db.The full, detailed Nikah Counselor Agreement and Code of Conduct follow during onboarding — this page is a summary to get you started right.') }}</p>
                <p class="text-sm text-gray-600 mb-4" dir="rtl">مکمل اور تفصیلی نکاح کاؤنسلر معاہدہ اور ضابطہ اخلاق تربیت کے دوران فراہم کیا جائے گا — یہ صفحہ آپ کو صحیح طریقے سے شروعات کے لیے ایک خلاصہ ہے۔</p>
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    <a href="{{ route('nikah-counselor.code-of-conduct') }}" class="btn-base btn-teal px-6 py-2.5 text-sm">📜 {{ __('db.Read the Code of Conduct') }}</a>
                    <a href="{{ url('/') }}" class="btn-base px-6 py-2.5 text-sm border border-gray-300 text-gray-600 hover:bg-gray-50">🏠 {{ __('db.Back to Home') }}</a>
                </div>
            </div>

        </div>
    </section>
</x-guest-layout>
