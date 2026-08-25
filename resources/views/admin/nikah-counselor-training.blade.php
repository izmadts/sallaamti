<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-700 font-semibold">📘 Nikah Counselor Module — Complete Training Reference</span>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ lang: 'en' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-600">For admin and senior staff training a new Nikah Counselor — or training each other on how the module actually works today. Covers every feature, every status, both sides (counselor + admin), in order.</p>
                    <p class="text-xs text-gray-400 mt-1">Each section below is collapsible — click a heading to open/close it. Everything is bilingual; switch language any time without losing your place.</p>
                </div>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1 flex-shrink-0">
                    <button type="button" @click="lang = 'en'" :class="lang === 'en' ? 'bg-white shadow-sm text-teal-700' : 'text-gray-500'" class="text-xs font-semibold px-3 py-1.5 rounded-md transition">English</button>
                    <button type="button" @click="lang = 'ur'" :class="lang === 'ur' ? 'bg-white shadow-sm text-teal-700' : 'text-gray-500'" class="text-xs font-semibold px-3 py-1.5 rounded-md transition">اردو</button>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 1. OVERVIEW --}}
            {{-- ============================================================ --}}
            <details open class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>1️⃣ Module Overview</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <p>The Nikah Counselor (also called "Match Maker") module is a paid referral/sales role: a real person who finds clients, helps them register a genuine Nikah profile, understands what they're looking for, and proposes candidates who actually fit — earning commission on real, verified work. It exists to solve one problem: self-service registration is a barrier for people who reach Sallaamti through WhatsApp/Facebook/Instagram and would otherwise go nowhere.</p>
                        <p>Five things to hold in your head before anything else makes sense:</p>
                        <ul>
                            <li><strong>A Lead</strong> is a counselor's client record — a name, a phone number, a status. It exists before any real Sallaamti account does.</li>
                            <li><strong>A NikahProfile</strong> is the real, registered matrimonial profile — created either by the client themselves or by the counselor on their behalf (the "walk-in wizard").</li>
                            <li><strong>A MatchmakerApplication</strong> is the counselor's own certification record — separate from the Lead/client system entirely. It's how someone <em>becomes</em> a counselor.</li>
                            <li><strong>The client's one link</strong> is the single standing page (phone + last-7-digits gate, no login) where every single client-facing interaction happens — consent, documents, package payment, proposal responses, status. As of today, there is exactly one of these per client, not several.</li>
                            <li><strong>Commission</strong> is calculated automatically from real events (a profile getting verified+paid, a package getting confirmed) — a counselor never types in their own commission.</li>
                        </ul>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <p>نکاح کاؤنسلر (جسے "میچ میکر" بھی کہا جاتا ہے) ایک ادائیگی والا ریفرل/سیلز کردار ہے: ایک حقیقی شخص جو کلائنٹس تلاش کرتا ہے، ان کی حقیقی نکاح پروفائل بنانے میں مدد دیتا ہے، یہ سمجھتا ہے کہ وہ کیا تلاش کر رہے ہیں، اور ایسے امیدوار تجویز کرتا ہے جو واقعی موزوں ہوں — اور حقیقی، تصدیق شدہ کام پر کمیشن کماتا ہے۔ یہ ایک مسئلے کو حل کرنے کے لیے بنایا گیا ہے: خود رجسٹریشن ان لوگوں کے لیے مشکل ہے جو واٹس ایپ/فیس بک/انسٹاگرام سے سلامتی تک پہنچتے ہیں اور ورنہ کہیں نہ جاتے۔</p>
                        <p>پانچ چیزیں ذہن میں رکھیں، باقی سب خودبخود سمجھ آ جائے گا:</p>
                        <ul>
                            <li><strong>لیڈ (Lead)</strong> کاؤنسلر کا کلائنٹ ریکارڈ ہے — ایک نام، ایک فون نمبر، ایک حیثیت۔ یہ کسی حقیقی سلامتی اکاؤنٹ سے پہلے موجود ہوتا ہے۔</li>
                            <li><strong>نکاح پروفائل (NikahProfile)</strong> حقیقی، رجسٹرڈ میٹریمونیل پروفائل ہے — خود کلائنٹ یا کاؤنسلر (واک اِن ویزرڈ کے ذریعے) کی طرف سے بنائی جاتی ہے۔</li>
                            <li><strong>میچ میکر اپلیکیشن (MatchmakerApplication)</strong> کاؤنسلر کا اپنا سرٹیفیکیشن ریکارڈ ہے — لیڈ/کلائنٹ نظام سے مکمل الگ۔ یہی وہ عمل ہے جس سے کوئی شخص کاؤنسلر <em>بنتا</em> ہے۔</li>
                            <li><strong>کلائنٹ کا ایک لنک</strong> وہ واحد مستقل صفحہ ہے (فون + آخری 7 ہندسے، بغیر لاگ اِن) جہاں کلائنٹ کی ہر بات چیت ہوتی ہے — رضامندی، دستاویزات، پیکج کی ادائیگی، تجاویز کے جوابات، حیثیت۔ آج کی تاریخ میں ہر کلائنٹ کا صرف ایک ہی ایسا لنک ہوتا ہے، کئی نہیں۔</li>
                            <li><strong>کمیشن</strong> خودکار طور پر حقیقی واقعات سے شمار ہوتا ہے (پروفائل کا تصدیق شدہ اور ادا شدہ ہونا، پیکج کی تصدیق ہونا) — کاؤنسلر کبھی اپنا کمیشن خود درج نہیں کرتا۔</li>
                        </ul>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 2. ACCESS LEVELS --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>2️⃣ Who Can Do What</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <table class="w-full text-sm border-collapse">
                            <thead><tr class="text-left border-b"><th class="py-2 pr-3">Role</th><th class="py-2 pr-3">Sees</th><th class="py-2">Cannot do</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="py-2 pr-3 align-top"><strong>Plain Matchmaker</strong></td><td class="py-2 pr-3 align-top">Only their own assigned clients, in the Match Maker Desk</td><td class="py-2 align-top">See other counselors' clients; approve/reject profiles; confirm payments; view CNIC/phone</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top"><strong>Senior Matchmaker</strong> (<code>leads.manage</code> permission, grantable to any matchmaker)</td><td class="py-2 pr-3 align-top">Every matchmaker's clients (via a filter), can reassign between counselors</td><td class="py-2 align-top">Everything a plain matchmaker can't — this permission is narrow, on purpose</td></tr>
                                <tr><td class="py-2 pr-3 align-top"><strong>Admin</strong></td><td class="py-2 pr-3 align-top">Everything — every counselor, every client's consent/proposals/payments, full commission ledger</td><td class="py-2 align-top">—</td></tr>
                            </tbody>
                        </table>
                        <p class="text-xs text-gray-400 mt-2">Granting "Senior Matchmaker" is done from Users → Roles (the underlying permission is <code>leads.manage</code>) — it does not touch commission tier or level, which are separate concepts (section 3).</p>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <table class="w-full text-sm border-collapse">
                            <thead><tr class="text-right border-b"><th class="py-2 pl-3">کردار</th><th class="py-2 pl-3">دیکھ سکتا ہے</th><th class="py-2">نہیں کر سکتا</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="py-2 pl-3 align-top"><strong>عام میچ میکر</strong></td><td class="py-2 pl-3 align-top">صرف اپنے تفویض کردہ کلائنٹس، میچ میکر ڈیسک میں</td><td class="py-2 align-top">دوسرے کاؤنسلرز کے کلائنٹس دیکھنا؛ پروفائلز کی منظوری/مسترد کرنا؛ ادائیگی کی تصدیق کرنا؛ شناختی کارڈ/فون دیکھنا</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top"><strong>سینیئر میچ میکر</strong> (<code>leads.manage</code> اجازت، کسی بھی میچ میکر کو دی جا سکتی ہے)</td><td class="py-2 pl-3 align-top">ہر میچ میکر کے کلائنٹس (فلٹر کے ذریعے)، اور کاؤنسلرز کے درمیان تفویض بدل سکتا ہے</td><td class="py-2 align-top">وہ سب کچھ جو عام میچ میکر نہیں کر سکتا — یہ اجازت جان بوجھ کر محدود رکھی گئی ہے</td></tr>
                                <tr><td class="py-2 pl-3 align-top"><strong>ایڈمن</strong></td><td class="py-2 pl-3 align-top">سب کچھ — ہر کاؤنسلر، ہر کلائنٹ کی رضامندی/تجاویز/ادائیگیاں، مکمل کمیشن لیجر</td><td class="py-2 align-top">—</td></tr>
                            </tbody>
                        </table>
                        <p class="text-xs text-gray-400 mt-2">"سینیئر میچ میکر" کی اجازت یوزرز → رولز سے دی جاتی ہے (بنیادی اجازت <code>leads.manage</code> ہے) — اس کا کمیشن ٹیئر یا سطح سے کوئی تعلق نہیں، یہ الگ تصورات ہیں (سیکشن 3)۔</p>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 3. CERTIFICATION PIPELINE --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>3️⃣ How Someone Becomes (and Levels Up as) a Counselor</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <h4>The Application Pipeline</h4>
                        <p>A guest applies publicly at <code>/nikah-counselor/apply</code> — no account needed. The application moves through 10 admin-controlled stages: <strong>applied → identity_verified → references_checked → interviewed → agreement_signed → nda_signed → training → assessed → probation → certified</strong> (admin can move it to any stage from the dropdown at <strong>Admin → Nikah Counselors</strong> — it's not strictly forward-only). Two terminal exits: <strong>rejected</strong> (Sallaamti's decision) and <strong>withdrawn</strong> (the applicant's own choice to leave — a real button, added recently after this status existed unreachable for a long time).</p>
                        <h4>Agreement &amp; NDA</h4>
                        <p>Before certification, the applicant must accept a real Agreement + NDA themselves — admin presses <strong>Generate Agreement Link</strong> on their application page, sends it, and they read and accept it on their own (same last-7-digits phone gate as everything else). Certification is <strong>hard-blocked</strong> until this is done — the pipeline dropdown alone doesn't imply it happened.</p>
                        <h4>What "Certified" Actually Does</h4>
                        <p>Moving an application to <strong>certified</strong> automatically: creates their real Sallaamti account (with the <code>matchmaker</code> role), mints a Counselor ID (<code>MM-PK-######</code>), generates their referral link + QR code, and issues their ID card certificate — all in one action, no manual follow-up steps.</p>
                        <h4>Levels — Now Automatic</h4>
                        <p>Four levels: 🥉 Nikah Counselor → 🥈 Certified Nikah Counselor → 🥇 Senior Nikah Counselor → ⭐ Regional Nikah Coordinator. <strong>Level directly sets commission rate</strong> — this is not cosmetic. A daily scheduled job promotes a counselor to the next level only when they clear <em>all three</em> of these together:</p>
                        <table class="w-full text-sm border-collapse">
                            <thead><tr class="text-left border-b"><th class="py-2 pr-3">Next level</th><th class="py-2 pr-3">Verified profiles</th><th class="py-2 pr-3">Quality score</th><th class="py-2">Tenure</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="py-2 pr-3">🥈 Certified</td><td class="py-2 pr-3">5</td><td class="py-2 pr-3">60%</td><td class="py-2">30 days</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3">🥇 Senior</td><td class="py-2 pr-3">20</td><td class="py-2 pr-3">75%</td><td class="py-2">90 days</td></tr>
                                <tr><td class="py-2 pr-3">⭐ Regional Coordinator</td><td class="py-2 pr-3">50</td><td class="py-2 pr-3">85%</td><td class="py-2">180 days</td></tr>
                            </tbody>
                        </table>
                        <p>Promotion is <strong>one-way only</strong> — it never auto-demotes (that stays a manual admin action from the application page). Both the counselor and every admin get notified when it happens — never a silent change to someone's pay rate. Quality Score itself = (Verification Rate × 45%) + (Paid Conversion Rate × 45%) + (Compliance Rate × 10%) — visible to the counselor on <strong>My Performance</strong>, and to admin on that counselor's application page (with the same "X to go" breakdown toward their next level).</p>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <h4>درخواست کا مرحلہ وار عمل</h4>
                        <p>کوئی بھی شخص <code>/nikah-counselor/apply</code> پر بغیر اکاؤنٹ کے عوامی طور پر درخواست دے سکتا ہے۔ درخواست ایڈمن کے کنٹرول میں ان 10 مراحل سے گزرتی ہے: <strong>درخواست دی → شناخت کی تصدیق → حوالہ جات کی جانچ → انٹرویو → کاؤنسلر معاہدہ دستخط شدہ → رازداری/NDA دستخط شدہ → تربیت → تشخیص پاس → پروبیشن → سرٹیفائیڈ</strong> (ایڈمن ڈراپ ڈاؤن سے کسی بھی مرحلے پر لے جا سکتا ہے — یہ سختی سے آگے ہی آگے نہیں ہے)۔ دو حتمی نتائج: <strong>مسترد</strong> (سلامتی کا فیصلہ) اور <strong>واپس لی گئی</strong> (درخواست دہندہ کا اپنا فیصلہ — ایک حقیقی بٹن، جو حال ہی میں شامل کیا گیا کیونکہ یہ حیثیت طویل عرصے تک ناقابلِ رسائی رہی)۔</p>
                        <h4>معاہدہ اور رازداری کا معاہدہ (NDA)</h4>
                        <p>سرٹیفیکیشن سے پہلے، درخواست دہندہ کو خود ایک حقیقی معاہدہ اور NDA قبول کرنا ہوتا ہے — ایڈمن ان کے درخواست صفحے پر <strong>معاہدے کا لنک بنائیں</strong> دباتا ہے، بھیجتا ہے، اور وہ خود اسے پڑھ کر قبول کرتے ہیں (وہی آخری 7 ہندسوں والا فون گیٹ)۔ سرٹیفیکیشن اس کے بغیر <strong>مکمل طور پر روکی جاتی ہے</strong> — صرف ڈراپ ڈاؤن سے یہ ظاہر نہیں ہوتا کہ یہ ہو چکا ہے۔</p>
                        <h4>"سرٹیفائیڈ" ہونے پر اصل میں کیا ہوتا ہے</h4>
                        <p>درخواست کو <strong>سرٹیفائیڈ</strong> کرنے پر خودکار طور پر: ان کا حقیقی سلامتی اکاؤنٹ بنتا ہے (میچ میکر رول کے ساتھ)، کاؤنسلر آئی ڈی (<code>MM-PK-######</code>) بنتی ہے، ان کا ریفرل لنک + QR کوڈ تیار ہوتا ہے، اور ان کا آئی ڈی کارڈ سرٹیفکیٹ جاری ہوتا ہے — سب ایک ہی کارروائی میں، کوئی دستی مرحلہ نہیں۔</p>
                        <h4>سطحیں — اب خودکار</h4>
                        <p>چار سطحیں: 🥉 نکاح کاؤنسلر → 🥈 سرٹیفائیڈ نکاح کاؤنسلر → 🥇 سینیئر نکاح کاؤنسلر → ⭐ ریجنل نکاح کوآرڈینیٹر۔ <strong>سطح براہ راست کمیشن کی شرح طے کرتی ہے</strong> — یہ محض سجاوٹ نہیں۔ ایک روزانہ چلنے والا خودکار عمل کاؤنسلر کو اگلی سطح پر تب ہی بڑھاتا ہے جب وہ ان تینوں شرائط کو ایک ساتھ پورا کرے:</p>
                        <table class="w-full text-sm border-collapse">
                            <thead><tr class="text-right border-b"><th class="py-2 pl-3">اگلی سطح</th><th class="py-2 pl-3">تصدیق شدہ پروفائلز</th><th class="py-2 pl-3">کوالٹی سکور</th><th class="py-2">مدت</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="py-2 pl-3">🥈 سرٹیفائیڈ</td><td class="py-2 pl-3">5</td><td class="py-2 pl-3">60%</td><td class="py-2">30 دن</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3">🥇 سینیئر</td><td class="py-2 pl-3">20</td><td class="py-2 pl-3">75%</td><td class="py-2">90 دن</td></tr>
                                <tr><td class="py-2 pl-3">⭐ ریجنل کوآرڈینیٹر</td><td class="py-2 pl-3">50</td><td class="py-2 pl-3">85%</td><td class="py-2">180 دن</td></tr>
                            </tbody>
                        </table>
                        <p>ترقی <strong>صرف ایک طرفہ</strong> ہے — کبھی خودکار طور پر نیچے نہیں آتی (یہ اب بھی درخواست صفحے سے دستی ایڈمن کارروائی ہے)۔ جب یہ ہو تو کاؤنسلر اور ہر ایڈمن دونوں کو مطلع کیا جاتا ہے — کسی کی آمدنی کی شرح میں کبھی خاموشی سے تبدیلی نہیں آتی۔ کوالٹی سکور خود = (ویریفیکیشن ریٹ × 45%) + (ادا شدہ کنورژن ریٹ × 45%) + (کمپلائنس ریٹ × 10%) — کاؤنسلر کو <strong>میری کارکردگی</strong> پر نظر آتا ہے، اور ایڈمن کو اسی کاؤنسلر کے درخواست صفحے پر (اگلی سطح تک "کتنا باقی ہے" کی وہی تفصیل کے ساتھ)۔</p>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 4. COUNSELOR WORKSPACE --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>4️⃣ The Counselor's Own Workspace — Every Feature</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <p>Logging in as a matchmaker goes straight to the <strong>Match Maker Desk</strong> (<code>dashboard.matchmaker</code>) — a completely separate themed area, not the general member dashboard. Sidebar, top to bottom:</p>
                        <ul>
                            <li><strong>Dashboard</strong> — New Leads / Follow-ups Due / Registered Clients counts, Active Proposal Batches, Awaiting Client Response, Interested (7 days), a Follow-ups list, Recent Clients, Recent Activity. Everything scoped to their own assigned leads only.</li>
                            <li><strong>My Clients</strong> — every Lead assigned to them (or, with the Senior permission, everyone's, filterable). Statuses: <strong>new → contacted → interested → registered</strong>, or <strong>not_interested / closed</strong>.</li>
                            <li><strong>Add Client</strong> — name, phone/email, gender, source (facebook/instagram/whatsapp/website/phone/referral/manual/other). Saving with a phone number auto-generates the client's one link right there — no separate step.</li>
                            <li><strong>Browse Profiles</strong> — the searchable catalog (filter gender/city/sect/marital status/age). CNIC and photo are never shown here, only what a counselor actually needs to judge fit. A "Request Contact" action exists for when two families should genuinely be introduced — admin approves it, the counselor never contacts anyone directly outside the system.</li>
                            <li><strong>My Contact Requests</strong> — status of every Request Contact they've sent (pending/approved).</li>
                            <li><strong>Register Walk-in Client</strong> (only if permission-granted) — starts the full registration wizard directly for someone standing in front of them, no prior Lead record needed. If they can't collect CNIC/photo in person (a remote client), a checkbox on the Verification step skips those — the client's own link then shows a document-upload step next time they visit.</li>
                            <li><strong>My Commissions</strong> — their own ledger only (pending/approved/paid/flagged), read-only — they can never approve or pay their own entries.</li>
                            <li><strong>My Performance</strong> — Quality Score breakdown, level, and the exact numeric progress toward their next auto-promotion.</li>
                        </ul>
                        <h4>Inside a Client's Page (tabs)</h4>
                        <ul>
                            <li><strong>Overview</strong> — status, the Copy Link button for their one link (the raw link/URL is never shown as text, only the button — see section 6), consent controls, package status.</li>
                            <li><strong>Requirements</strong> — age range, city, education, marital status, etc., each tagged Must Have / Preferred / Flexible. Never shown to the client — this is what powers Suggested Matches.</li>
                            <li><strong>Shortlist</strong> — 🎯 Suggested Matches at the top (auto-ranked against Requirements, tagged 🟢 High / 🟡 Medium / ⚪ Low — a candidate failing any Must Have is excluded entirely, not just scored low), then manual search below it. Adding here is just a working list — nothing sent yet.</li>
                            <li><strong>Proposal Batches</strong> — build a batch of up to 5 from the shortlist, each with an optional "why this match" note, then Mark as Sent. Blocked until: linked profile + active consent + active package all exist. No per-candidate link any more — sent candidates appear directly on the client's own one link, and their response reflects back here in real time.</li>
                            <li><strong>Timeline</strong> — every meaningful action on this client, automatically logged, in order. This is the fastest way to pick up someone else's client if you're covering for them.</li>
                        </ul>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <p>میچ میکر کے طور پر لاگ اِن کرنے پر سیدھا <strong>میچ میکر ڈیسک</strong> (<code>dashboard.matchmaker</code>) پر پہنچتے ہیں — ایک بالکل الگ تھیم والا حصہ، عام ممبر ڈیش بورڈ نہیں۔ سائیڈبار، اوپر سے نیچے:</p>
                        <ul>
                            <li><strong>ڈیش بورڈ</strong> — نئی لیڈز / فالو اپ باقی / رجسٹرڈ کلائنٹس کی تعداد، فعال تجویز بیچز، کلائنٹ کے جواب کا انتظار، دلچسپی (7 دن)، فالو اپ کی فہرست، حالیہ کلائنٹس، حالیہ سرگرمی۔ سب کچھ صرف ان کے اپنے تفویض کردہ لیڈز تک محدود۔</li>
                            <li><strong>میرے کلائنٹس</strong> — ان کو تفویض کردہ ہر لیڈ (یا، سینیئر اجازت کے ساتھ، سب کے، فلٹر کے قابل)۔ حیثیتیں: <strong>نئی → رابطہ ہوا → دلچسپی → رجسٹرڈ</strong>، یا <strong>دلچسپی نہیں / بند</strong>۔</li>
                            <li><strong>کلائنٹ شامل کریں</strong> — نام، فون/ای میل، جنس، ذریعہ (فیس بک/انسٹاگرام/واٹس ایپ/ویب سائٹ/فون/ریفرل/دستی/دیگر)۔ فون نمبر کے ساتھ محفوظ کرنے پر کلائنٹ کا ایک لنک وہیں خودکار طور پر بن جاتا ہے — کوئی الگ مرحلہ نہیں۔</li>
                            <li><strong>پروفائلز براؤز کریں</strong> — قابلِ تلاش کیٹلاگ (جنس/شہر/فرقہ/ازدواجی حیثیت/عمر کے لحاظ سے فلٹر)۔ یہاں شناختی کارڈ اور تصویر کبھی نہیں دکھائی جاتی، صرف وہی جو کاؤنسلر کو موزونیت جانچنے کے لیے درکار ہے۔ "رابطے کی درخواست" اس وقت کے لیے موجود ہے جب دو خاندانوں کا حقیقی تعارف کروانا ہو — ایڈمن اسے منظور کرتا ہے، کاؤنسلر کبھی نظام سے باہر براہ راست رابطہ نہیں کرتا۔</li>
                            <li><strong>میری رابطہ درخواستیں</strong> — انہوں نے جو بھی رابطے کی درخواستیں بھیجی ہیں ان کی حیثیت (زیرِ التوا/منظور شدہ)۔</li>
                            <li><strong>واک اِن کلائنٹ رجسٹر کریں</strong> (صرف اجازت کے ساتھ) — کسی کے سامنے کھڑے شخص کے لیے مکمل رجسٹریشن ویزرڈ براہ راست شروع کرتا ہے، پہلے سے لیڈ ریکارڈ کی ضرورت نہیں۔ اگر شناختی کارڈ/تصویر ذاتی طور پر جمع نہ کر سکیں (دور بیٹھا کلائنٹ)، ویریفیکیشن مرحلے پر ایک چیک باکس انہیں چھوڑ دیتا ہے — کلائنٹ کا اپنا لنک اگلی بار دستاویز اپلوڈ کا مرحلہ دکھائے گا۔</li>
                            <li><strong>میرے کمیشن</strong> — صرف ان کا اپنا لیجر (زیرِ التوا/منظور شدہ/ادا شدہ/نشان زد شدہ)، صرف دیکھنے کے لیے — وہ کبھی اپنی انٹریز خود منظور یا ادا نہیں کر سکتے۔</li>
                            <li><strong>میری کارکردگی</strong> — کوالٹی سکور کی تفصیل، سطح، اور اگلی خودکار ترقی کی طرف صحیح عددی پیش رفت۔</li>
                        </ul>
                        <h4>کلائنٹ کے صفحے کے اندر (ٹیبز)</h4>
                        <ul>
                            <li><strong>خلاصہ</strong> — حیثیت، ان کے ایک لنک کے لیے کاپی لنک بٹن (اصل لنک/یو آر ایل کبھی متن کے طور پر نہیں دکھایا جاتا، صرف بٹن — سیکشن 6 دیکھیں)، رضامندی کے کنٹرولز، پیکج کی حیثیت۔</li>
                            <li><strong>ضروریات</strong> — عمر کی حد، شہر، تعلیم، ازدواجی حیثیت وغیرہ، ہر ایک لازمی/ترجیحی/لچکدار کے طور پر نشان زد۔ کبھی کلائنٹ کو نہیں دکھایا جاتا — یہی تجویز کردہ میچز کو طاقت دیتا ہے۔</li>
                            <li><strong>شارٹ لسٹ</strong> — اوپر 🎯 تجویز کردہ میچز (ضروریات کے مقابلے میں خودکار درجہ بندی، 🟢 اعلیٰ / 🟡 درمیانہ / ⚪ کم نشان زد — کوئی امیدوار جو کوئی لازمی شرط پوری نہ کرے مکمل طور پر خارج ہوتا ہے، صرف کم سکور نہیں ملتا)، پھر نیچے دستی تلاش۔ یہاں شامل کرنا صرف ایک ورکنگ فہرست ہے — ابھی کچھ نہیں بھیجا جاتا۔</li>
                            <li><strong>تجویز بیچز</strong> — شارٹ لسٹ سے 5 تک کا بیچ بنائیں، ہر ایک کے ساتھ اختیاری "یہ میچ کیوں" نوٹ، پھر بھیجا گیا نشان زد کریں۔ یہ اس وقت تک روکا جاتا ہے جب تک: منسلک پروفائل + فعال رضامندی + فعال پیکج، تینوں موجود نہ ہوں۔ اب فی امیدوار الگ لنک نہیں — بھیجے گئے امیدوار سیدھا کلائنٹ کے اپنے ایک لنک پر نظر آتے ہیں، اور ان کا جواب فوری طور پر یہاں واپس نظر آتا ہے۔</li>
                            <li><strong>ٹائم لائن</strong> — اس کلائنٹ پر ہر اہم عمل، خودکار طور پر ترتیب وار ریکارڈ ہوتا ہے۔ اگر آپ کسی اور کی جگہ کام سنبھال رہے ہوں تو یہ کسی کلائنٹ کو سب سے تیزی سے سمجھنے کا طریقہ ہے۔</li>
                        </ul>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 5. CLIENT JOURNEY --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>5️⃣ The Client's Journey — What They See On Their One Link</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <p>The client's link (<code>/p/{lead}?t=...</code>) needs no account. Every visit re-checks the last 7 digits of their phone — nothing about "already unlocked" survives between visits, on purpose, since the link itself never expires and may sit in a chat thread indefinitely.</p>
                        <p>The page shows <strong>exactly one thing at a time</strong> — whichever of these is next, in this order, never a long scroll of everything at once:</p>
                        <ol>
                            <li><strong>Confirm a pending consent</strong> (if any) — one at a time if there's more than one.</li>
                            <li><strong>Upload verification documents</strong> — only if their profile exists but is missing CNIC/photo (the remote-registration case).</li>
                            <li><strong>Choose &amp; pay for a package</strong> — sees the account details (JazzCash/EasyPaisa/bank, tap-to-copy), uploads a screenshot. Goes to admin review — never auto-confirmed.</li>
                            <li><strong>Review a proposed match</strong> — one whole batch at a time (candidates in a batch are a curated set meant to be compared together), full detail card, Interested/Maybe/Not Interested.</li>
                            <li><strong>Set up quick access</strong> — optional, skippable, one-time-only offer to create a 4-digit PIN turning their link into a real <code>sallaamti.com</code> login for later. Say "not now" and it never asks again.</li>
                        </ol>
                        <p>When nothing's waiting, it shows <strong>"You're All Caught Up"</strong> instead of an empty page, with a small "+N more things waiting after this" hint whenever there's more queued behind the current step. A collapsed <strong>"Your Full Status &amp; History"</strong> section always holds the complete picture — status, active package, every proposal batch and response, full activity log — available on demand, just never competing with whatever actually needs their attention right now.</p>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <p>کلائنٹ کے لنک (<code>/p/{lead}?t=...</code>) کے لیے کسی اکاؤنٹ کی ضرورت نہیں۔ ہر وزٹ پر ان کے فون نمبر کے آخری 7 ہندسے دوبارہ چیک کیے جاتے ہیں — "پہلے سے کھلا ہوا" کچھ بھی اگلی وزٹ تک برقرار نہیں رہتا، جان بوجھ کر، کیونکہ لنک خود کبھی ختم نہیں ہوتا اور ہمیشہ کے لیے کسی چیٹ میں پڑا رہ سکتا ہے۔</p>
                        <p>صفحہ ہمیشہ <strong>بالکل ایک ہی چیز</strong> دکھاتا ہے — جو بھی اگلا ہو، اسی ترتیب میں، کبھی ایک ساتھ لمبی فہرست نہیں:</p>
                        <ol>
                            <li><strong>زیرِ التوا رضامندی کی تصدیق</strong> (اگر ہو) — ایک وقت میں ایک، اگر ایک سے زیادہ ہوں۔</li>
                            <li><strong>تصدیقی دستاویزات اپلوڈ کریں</strong> — صرف اگر پروفائل موجود ہو لیکن شناختی کارڈ/تصویر باقی ہو (دور سے رجسٹریشن کی صورت)۔</li>
                            <li><strong>پیکج منتخب اور ادا کریں</strong> — اکاؤنٹ کی تفصیلات دیکھتے ہیں (جاز کیش/ایزی پیسہ/بینک، ایک کلک میں کاپی)، رسید اپلوڈ کرتے ہیں۔ ایڈمن کے جائزے میں جاتا ہے — کبھی خودکار تصدیق نہیں ہوتی۔</li>
                            <li><strong>تجویز کردہ رشتے کا جائزہ</strong> — ایک وقت میں ایک پورا بیچ (بیچ میں امیدوار ایک ساتھ موازنے کے لیے چنے گئے مجموعے ہیں)، مکمل تفصیلی کارڈ، دلچسپی ہے/شاید/دلچسپی نہیں۔</li>
                            <li><strong>فوری رسائی ترتیب دیں</strong> — اختیاری، چھوڑی جا سکنے والی، صرف ایک بار کی پیشکش، 4 ہندسوں کا PIN بنانے کی تاکہ ان کا لنک بعد میں ایک حقیقی <code>sallaamti.com</code> لاگ اِن بن جائے۔ "ابھی نہیں" کہیں تو یہ دوبارہ کبھی نہیں پوچھے گا۔</li>
                        </ol>
                        <p>جب کچھ بھی ان کا منتظر نہ ہو تو خالی صفحے کی بجائے <strong>"آپ کا سب کچھ مکمل ہے"</strong> دکھاتا ہے، اور اگر قطار میں کچھ اور باقی ہو تو ایک چھوٹا "+N اور چیزیں باقی ہیں" اشارہ بھی۔ ایک بند شدہ <strong>"آپ کی مکمل صورتحال اور تاریخ"</strong> سیکشن ہمیشہ مکمل تصویر رکھتا ہے — حیثیت، فعال پیکج، ہر تجویز بیچ اور جواب، مکمل سرگرمی کا ریکارڈ — طلب پر دستیاب، بس اس چیز کے ساتھ مقابلہ نہیں کرتا جس پر ابھی توجہ درکار ہے۔</p>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 6. PRIVACY & SECURITY --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>6️⃣ Privacy, Security &amp; Conduct Rules</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <p>These are enforced by the system, not just written policy — train against what actually happens on screen:</p>
                        <ul>
                            <li><strong>A counselor never sees a client's real phone number.</strong> It shows masked everywhere on their own screens (e.g. <code>03••••••••43</code>).</li>
                            <li><strong>A counselor never sees the raw link either</strong> — only a Copy Link button. They paste it wherever they're already messaging the client; the link text itself never renders as visible text on a Sallaamti page.</li>
                            <li><strong>CNIC and photo are never shown to a counselor</strong> — not a browsed candidate's, not even their own client's (unless they personally collected it in person during walk-in registration).</li>
                            <li><strong>Every visit to a client's link is logged</strong> — time, approximate location, device — a real record if something ever needs checking.</li>
                            <li><strong>No CNIC over WhatsApp, ever</strong> — not the number, not a photo of it. Remote registration uses the checkbox + link-upload flow instead (section 4).</li>
                            <li><strong>No cash collection</strong> — every payment goes through Sallaamti's own channels (package payment self-service, or the counselor relaying a verification-fee receipt into the same admin review queue a self-service payment uses).</li>
                            <li><strong>No private database</strong> — no personal spreadsheet, no saved contacts list, nothing outside the system.</li>
                            <li><strong>No recruitment commission</strong> — a counselor is paid only for their own direct work, never for bringing in other counselors (single-level only, no MLM, this is a hard rule in the Agreement/NDA they signed).</li>
                            <li><strong>Client ownership stays with Sallaamti</strong> — if a counselor leaves, every client relationship stays with the platform, not the person.</li>
                        </ul>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <p>یہ نظام کی طرف سے نافذ کی گئی باتیں ہیں، صرف تحریری پالیسی نہیں — اسکرین پر جو اصل میں ہوتا ہے اسی کے مطابق تربیت دیں:</p>
                        <ul>
                            <li><strong>کاؤنسلر کبھی کلائنٹ کا اصل موبائل نمبر نہیں دیکھتا۔</strong> یہ ان کی اپنی سکرین پر ہر جگہ چھپا ہوا نظر آتا ہے (مثلاً <code>03••••••••43</code>)۔</li>
                            <li><strong>کاؤنسلر کبھی خام لنک بھی نہیں دیکھتا</strong> — صرف ایک کاپی لنک بٹن۔ وہ اسے وہیں پیسٹ کرتے ہیں جہاں پہلے سے کلائنٹ سے بات ہو رہی ہو؛ لنک کا متن کبھی سلامتی کے کسی صفحے پر نظر آنے والے متن کے طور پر نہیں دکھایا جاتا۔</li>
                            <li><strong>شناختی کارڈ اور تصویر کبھی کاؤنسلر کو نہیں دکھائی جاتی</strong> — نہ براؤز کیے گئے امیدوار کی، نہ اپنے کلائنٹ کی (سوائے اس کے کہ انہوں نے واک اِن رجسٹریشن کے دوران ذاتی طور پر جمع کی ہو)۔</li>
                            <li><strong>کلائنٹ کے لنک کی ہر وزٹ ریکارڈ ہوتی ہے</strong> — وقت، تخمینی مقام، آلہ — اگر کبھی کچھ چیک کرنے کی ضرورت پڑے تو ایک حقیقی ریکارڈ۔</li>
                            <li><strong>واٹس ایپ پر کبھی شناختی کارڈ نہیں</strong> — نہ نمبر، نہ اس کی تصویر۔ دور سے رجسٹریشن کے لیے چیک باکس + لنک اپلوڈ کا عمل استعمال ہوتا ہے (سیکشن 4)۔</li>
                            <li><strong>نقد وصولی نہیں</strong> — ہر ادائیگی سلامتی کے اپنے ذرائع سے ہوتی ہے (پیکج کی خود ادائیگی، یا کاؤنسلر کا تصدیقی فیس کی رسید اسی جائزہ قطار میں بھیجنا جو خود ادائیگی استعمال کرتی ہے)۔</li>
                            <li><strong>کوئی نجی ڈیٹا بیس نہیں</strong> — کوئی ذاتی فہرست، کوئی محفوظ کانٹیکٹس، نظام سے باہر کچھ نہیں۔</li>
                            <li><strong>بھرتی پر کمیشن نہیں</strong> — کاؤنسلر کو صرف اپنے براہ راست کام پر معاوضہ ملتا ہے، کبھی دوسرے کاؤنسلرز لانے پر نہیں (صرف ایک سطح، کوئی MLM نہیں، یہ ان کے دستخط شدہ معاہدے/NDA میں ایک سخت اصول ہے)۔</li>
                            <li><strong>کلائنٹ کی ملکیت سلامتی کے پاس رہتی ہے</strong> — اگر کاؤنسلر چھوڑ دے، تو ہر کلائنٹ کا تعلق پلیٹ فارم کے پاس رہتا ہے، شخص کے پاس نہیں۔</li>
                        </ul>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 7. COMMISSION ENGINE --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>7️⃣ Commission Engine</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <ul>
                            <li><strong>One universal rate per tier</strong> — not per package. All 4 levels each have one rate that applies to every matchmaking package equally (this was simplified from an earlier per-package-per-tier design that created 32 confusing rows).</li>
                            <li><strong>Two triggers:</strong> <code>verified_profile</code> fires automatically the moment a profile's verification fee payment is confirmed (credited to whoever registered them — the counselor if walk-in, or whoever's referral link they registered through). <code>package</code> fires when admin confirms a package payment.</li>
                            <li><strong>Ledger statuses:</strong> pending → approved → paid, with a separate <code>flagged</code> state any pending/approved entry can be marked with (e.g. suspected self-dealing) — a flagged entry can't be approved or paid until unflagged.</li>
                            <li><strong>Self-dealing guard:</strong> a counselor cannot approve, pay, flag, or reclassify their own commission entries — defense-in-depth on top of the role permissions.</li>
                            <li><strong>Recognition bonuses</strong> — a separate, admin-awarded ledger entry type, shown on the counselor's own performance stats.</li>
                        </ul>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <ul>
                            <li><strong>ہر ٹیئر کی ایک عالمی شرح</strong> — فی پیکج نہیں۔ چاروں سطحوں میں سے ہر ایک کی ایک شرح ہے جو ہر میچ میکنگ پیکج پر یکساں لاگو ہوتی ہے (یہ پہلے کے فی پیکج فی ٹیئر ڈیزائن سے آسان بنایا گیا جس میں 32 الجھی ہوئی قطاریں تھیں)۔</li>
                            <li><strong>دو ٹرگرز:</strong> <code>verified_profile</code> خودکار طور پر اس وقت چلتا ہے جب پروفائل کی تصدیقی فیس کی ادائیگی تصدیق ہو جائے (جس نے رجسٹر کیا اس کو کریڈٹ ملتا ہے — واک اِن کی صورت میں کاؤنسلر کو، یا جس کے ریفرل لنک سے رجسٹر ہوئے اسے)۔ <code>package</code> اس وقت چلتا ہے جب ایڈمن پیکج کی ادائیگی کی تصدیق کرے۔</li>
                            <li><strong>لیجر کی حیثیتیں:</strong> زیرِ التوا → منظور شدہ → ادا شدہ، ایک الگ <code>نشان زد</code> حیثیت کے ساتھ جو کسی بھی زیرِ التوا/منظور شدہ انٹری پر لگائی جا سکتی ہے (مثلاً خود غرضی کا شبہ) — نشان زد انٹری اس وقت تک منظور یا ادا نہیں ہو سکتی جب تک نشان نہ ہٹایا جائے۔</li>
                            <li><strong>خود غرضی سے تحفظ:</strong> کاؤنسلر اپنی کمیشن انٹریز خود منظور، ادا، نشان زد، یا دوبارہ درجہ بندی نہیں کر سکتا — رول کی اجازتوں کے علاوہ ایک اضافی تحفظ۔</li>
                            <li><strong>پہچان کے بونس</strong> — ایک الگ، ایڈمن کی طرف سے دیا جانے والا لیجر انٹری کی قسم، کاؤنسلر کی اپنی کارکردگی کے اعداد و شمار میں دکھائی جاتی ہے۔</li>
                        </ul>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 8. ADMIN TOOLKIT --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>8️⃣ Admin's Oversight Toolkit</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <ul>
                            <li><strong>Nikah Counselors</strong> (<code>/admin/matchmaker-applications</code>) — every application, its pipeline stage, agreement/NDA status, level (manual override), and now a performance snapshot (verified count, quality score, days certified, commission earned, and progress toward their next auto-promotion) — the same numbers the counselor sees on their own Performance page. Reject or Withdraw actions here.</li>
                            <li><strong>Leads</strong> (<code>/admin/leads</code>) — full cross-counselor visibility. The Lead detail page shows: the raw phone number and raw progress link (never shown to the counselor), a Consent card (every grant/revoke, plus pending requests), a Proposal Batches card (every batch and every candidate's response), and a package-payment review card whenever one is submitted — Confirm activates the package and fires commission, Reject requires a reason and stays visible on the page until resolved.</li>
                            <li><strong>Commission Ledger</strong> (<code>/admin/commissions/ledger</code>) — every entry across every counselor, Approve/Pay/Flag/Unflag/Reclassify actions.</li>
                            <li><strong>Commission Rules</strong> (<code>/admin/commissions/rules</code>) — the per-tier universal rates themselves.</li>
                        </ul>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <ul>
                            <li><strong>نکاح کاؤنسلرز</strong> (<code>/admin/matchmaker-applications</code>) — ہر درخواست، اس کا مرحلہ، معاہدہ/NDA کی حیثیت، سطح (دستی طور پر تبدیل کی جا سکتی ہے)، اور اب کارکردگی کا خلاصہ (تصدیق شدہ تعداد، کوالٹی سکور، سرٹیفائیڈ دنوں کی تعداد، کمایا گیا کمیشن، اور اگلی خودکار ترقی کی طرف پیش رفت) — وہی اعداد جو کاؤنسلر اپنے کارکردگی کے صفحے پر دیکھتا ہے۔ یہاں مسترد یا واپس لینے کی کارروائیاں۔</li>
                            <li><strong>لیڈز</strong> (<code>/admin/leads</code>) — ہر کاؤنسلر کی مکمل نظر۔ لیڈ کی تفصیل کا صفحہ دکھاتا ہے: خام فون نمبر اور خام پروگریس لنک (کبھی کاؤنسلر کو نہیں دکھایا جاتا)، رضامندی کارڈ (ہر منظوری/منسوخی، اور زیرِ التوا درخواستیں)، تجویز بیچز کارڈ (ہر بیچ اور ہر امیدوار کا جواب)، اور جب بھی جمع کروائی جائے تو پیکج کی ادائیگی کے جائزے کا کارڈ — تصدیق کریں پیکج کو فعال کرتی ہے اور کمیشن چلاتی ہے، مسترد کرنے کے لیے وجہ درکار ہے اور حل ہونے تک صفحے پر نظر آتا رہتا ہے۔</li>
                            <li><strong>کمیشن لیجر</strong> (<code>/admin/commissions/ledger</code>) — ہر کاؤنسلر کی ہر انٹری، منظور کریں/ادا کریں/نشان زد کریں/نشان ہٹائیں/دوبارہ درجہ بندی کی کارروائیاں۔</li>
                            <li><strong>کمیشن قوانین</strong> (<code>/admin/commissions/rules</code>) — فی ٹیئر عالمی شرحیں خود۔</li>
                        </ul>
                    </div>
                </div>
            </details>

            {{-- ============================================================ --}}
            {{-- 9. STATUS REFERENCE --}}
            {{-- ============================================================ --}}
            <details class="bg-white rounded-xl shadow-sm overflow-hidden group">
                <summary class="p-5 font-semibold text-gray-800 cursor-pointer flex items-center justify-between">
                    <span>9️⃣ Complete Status Reference</span>
                    <span class="text-gray-300 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-5 pb-6 border-t pt-4">
                    <div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">
                        <p>Every status value that exists in this module, in one place:</p>
                        <table class="w-full text-sm border-collapse">
                            <tbody>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">Lead.status</td><td class="py-2">new, contacted, interested, registered, not_interested, closed</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">Lead.package_payment_status</td><td class="py-2">null (nothing submitted), submitted, confirmed, rejected</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">MatchmakerApplication.status</td><td class="py-2">applied, identity_verified, references_checked, interviewed, agreement_signed, nda_signed, training, assessed, probation, certified, rejected, withdrawn</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">MatchmakerApplication.level</td><td class="py-2">nikah_counselor, certified_nikah_counselor, senior_nikah_counselor, regional_nikah_coordinator</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">ProposalBatch.status</td><td class="py-2">draft, ready, sent, partially_responded, completed, expired, cancelled</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">MatchProposal.status / response</td><td class="py-2">status: pending, sent, viewed, responded. response (once responded): interested, not_interested, maybe, need_more_information, no_response</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">MatchmakingConsent</td><td class="py-2">active (granted, not revoked) / revoked. Types: matchmaking_participation, contact_sharing, photo_sharing</td></tr>
                                <tr class="border-b"><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">MatchmakingConsentRequest.status</td><td class="py-2">pending, granted, declined</td></tr>
                                <tr><td class="py-2 pr-3 align-top font-semibold whitespace-nowrap">CommissionLedgerEntry.status</td><td class="py-2">pending, approved, paid — plus the independent flagged_at flag</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">
                        <p>اس ماڈیول میں موجود ہر حیثیت کی قدر، ایک ہی جگہ:</p>
                        <table class="w-full text-sm border-collapse">
                            <tbody>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">Lead.status</td><td class="py-2">نئی، رابطہ ہوا، دلچسپی، رجسٹرڈ، دلچسپی نہیں، بند</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">Lead.package_payment_status</td><td class="py-2">null (کچھ جمع نہیں کروایا)، جمع کروایا، تصدیق شدہ، مسترد</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">MatchmakerApplication.status</td><td class="py-2">درخواست دی، شناخت کی تصدیق، حوالہ جات کی جانچ، انٹرویو، معاہدہ دستخط شدہ، NDA دستخط شدہ، تربیت، تشخیص پاس، پروبیشن، سرٹیفائیڈ، مسترد، واپس لی گئی</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">MatchmakerApplication.level</td><td class="py-2">نکاح کاؤنسلر، سرٹیفائیڈ نکاح کاؤنسلر، سینیئر نکاح کاؤنسلر، ریجنل نکاح کوآرڈینیٹر</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">ProposalBatch.status</td><td class="py-2">مسودہ، تیار، بھیجا گیا، جزوی جواب، مکمل، میعاد ختم، منسوخ</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">MatchProposal.status / response</td><td class="py-2">status: زیرِ التوا، بھیجا گیا، دیکھا گیا، جواب دیا۔ response (جواب کے بعد): دلچسپی ہے، دلچسپی نہیں، شاید، مزید معلومات درکار، کوئی جواب نہیں</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">MatchmakingConsent</td><td class="py-2">فعال (دی گئی، منسوخ نہیں) / منسوخ۔ اقسام: میچ میکنگ شرکت، رابطہ شیئرنگ، تصویر شیئرنگ</td></tr>
                                <tr class="border-b"><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">MatchmakingConsentRequest.status</td><td class="py-2">زیرِ التوا، دی گئی، مسترد</td></tr>
                                <tr><td class="py-2 pl-3 align-top font-semibold whitespace-nowrap">CommissionLedgerEntry.status</td><td class="py-2">زیرِ التوا، منظور شدہ، ادا شدہ — نیز آزاد نشان زد فلیگ</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </details>

        </div>
    </div>

    <style>
        .prose h4 { color: #0d6b6b; margin-top: 1.5em; margin-bottom: 0.4em; font-weight: 700; font-size: 1rem; }
        .prose h4:first-child { margin-top: 0; }
        .prose p, .prose li { color: #4b5563; line-height: 1.7; }
        .prose ul, .prose ol { margin-top: 0.5em; }
        .prose table { margin-top: 0.75em; }
        .prose code { background: #f3f4f6; color: #831843; padding: 1px 6px; border-radius: 4px; font-size: 0.85em; }
        .prose-ur { font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Segoe UI', Tahoma, sans-serif; line-height: 2.4; font-size: 1.05rem; }
        .prose-ur h4 { line-height: 2; }
        .prose-ur table { line-height: 2; }
        details summary::-webkit-details-marker { display: none; }
    </style>
</x-admin-layout>
