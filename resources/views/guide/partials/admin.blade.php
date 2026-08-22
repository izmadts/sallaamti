<div x-show="lang === 'en'" x-cloak class="prose prose-sm max-w-none">

<h3>Dashboard</h3>
<p>Your <strong>Admin Dashboard</strong> is the starting point — every stat tile links straight to the relevant management page, grouped by area (Users, Nikah, Quran/Skills Courses, Quran Live Classes, Volunteers, Donations, Public Posts, Support). Yellow/red badges anywhere in the sidebar mean something is waiting on you.</p>

<h3>👥 Users & Permissions</h3>
<ul>
    <li><strong>All Users</strong> — view any account's details, edit their profile fields, send them a password-reset link, change their role, deactivate/reactivate an account (deactivating also hides their Nikah profile but never deletes any data), or delete an account entirely.</li>
    <li><strong>Roles Overview</strong> — create custom roles and delegate narrow permissions (view/manage/delete, per resource) to staff without giving them the full Admin role. Delegatable resources: Public Posts, Community Posts, Nikah, Donations, Volunteers, Family Support, Wall Moderation, Family Counseling. Give someone e.g. <code>wall.manage</code> so they can moderate the community feed without also touching Users, Settings, or Integrations.</li>
    <li>A handful of the most sensitive areas are <strong>always Admin-role-only</strong>, regardless of any permission granted: Users, Settings, Certificates, Quran/Skills Course content, Quran Live Class management, Subscribers, Localization, and live Integration credentials. This is intentional — these aren't "delegate to one narrow staff role" style decisions.</li>
</ul>

<h3>📢 Bulk Messages</h3>
<p>Send an email or WhatsApp broadcast to a filtered set of Users, or to newsletter Subscribers (email-only).</p>
<ul>
    <li>Filter recipients by module, role, city, join date, and more before composing.</li>
    <li>The email composer is a full rich-text editor (paste from Word/Docs) with pre-built, inline-CSS newsletter templates you can load and edit — plus an <strong>"+ Insert Name"</strong> button that adds a <code>@{{name}}</code> token, automatically replaced with each recipient's actual name when sent.</li>
    <li>Both channels auto-batch to stay within safe daily sending limits (500/day each — matching Gmail's practical cap and a conservative WhatsApp ceiling to avoid the sending number/account getting flagged or banned). A campaign bigger than today's remaining quota is split automatically, with the rest queued for 9am on the following day(s) — no manual splitting needed.</li>
    <li>The <strong>Bulk Messages</strong> history page shows a report card at the top (total campaigns, recipients reached, delivered/failed, delivery rate, today's quota used) and per-campaign delivery status.</li>
</ul>

<h3>💍 Nikah Management</h3>
<ul>
    <li><strong>Leads</strong> — the matchmaker CRM: capture a WhatsApp/Facebook/Instagram contact before they're a real account, track status/follow-ups/notes, "Convert to Client" to hand off into the same walk-in wizard below with their name/contact pre-filled, build a shortlist of candidates for them, and track a simple matchmaking package + price. Full admins see every matchmaker's leads; a matchmaker sees only their own.</li>
    <li><strong>All Profiles</strong> / <strong>Verifications</strong> — review new profiles (CNIC number + front/back photos), Approve or Reject with a reason. Bulk-approve is available for straightforward cases. Send a reminder to profiles stuck incomplete.</li>
    <li><strong>Payments</strong> — confirm or reject the one-time verification fee before a profile can go live; you can also record an offline payment manually.</li>
    <li><strong>Reports</strong> — safety reports members file against each other; view the conversation history attached to a report (if any) and suspend a profile if warranted. Dismiss reports that don't need action.</li>
    <li><strong>Contact Requests</strong> — matchmaker-initiated introductions awaiting your Approve/Deny decision.</li>
    <li><strong>Create Profile</strong> — the same walk-in wizard matchmakers use, for staff-assisted profile creation.</li>
    <li><strong>Guardian Verification</strong> — mark a guardian's identity/contact as verified for an extra trust signal on a profile.</li>
</ul>

<h3>📖 Quran & 💻 Digital Skills Courses</h3>
<p>Create/edit courses, lessons, and quizzes under <strong>Courses</strong> — filter by track (Quran vs. Skills) using the pill switch at the top; each course row shows a track badge. A course needs at least one published lesson (and ideally a quiz) before it's meaningfully useful to members. Lessons support text and video content; quizzes are multiple-choice with a pass threshold.</p>

<h3>🎥 Quran Live Classes</h3>
<ul>
    <li><strong>Course Levels</strong> (Quran Live Courses) — the levels students admit into (e.g. "Level 1 Nazrah"), each with its own monthly fee.</li>
    <li><strong>Class Groups</strong> — assign admitted students to a teacher-led group per level; a teacher must be vetted/approved before they can be assigned one.</li>
    <li><strong>Admissions</strong> — review pending admissions and assign each to a group, or reject with a reason.</li>
    <li><strong>Subscriptions</strong> — confirm or reject monthly payment submissions (screenshot + reference number) per student per month.</li>
</ul>

<h3>Community</h3>
<ul>
    <li><strong>Sallaamti Wall</strong> — approve or hide member-submitted dua requests (with a reason if hidden); filter by status (pending/approved/hidden/all).</li>
    <li><strong>Community Posts</strong> — your own admin-authored content for the Wall (activities, events, sermons), with optional auto-cross-posting to any connected social account (Facebook/Twitter/YouTube/TikTok/Threads — see Integrations), a bulk photo/video upload tool that turns old media into a scheduled queue, drag-to-reorder scheduling, and Pin to float a post to the top of the Wall.</li>
    <li><strong>Public Posts</strong> — member/staff-submitted stories awaiting review, with a stats row (pending/published/rejected/total). Approve to publish immediately (notifies the author, and the post goes live at its own public URL); Reject with a reason (also notifies the author). Anyone holding <code>posts.manage</code> gets their own submissions auto-published without needing review.</li>
    <li><strong>Volunteers</strong> — approve (auto-assigns the Volunteer role and emails a Volunteer ID Card PDF for registered applicants; emails guest applicants inviting them to register so their ID card can be issued) or reject (notifies the applicant either way, registered or guest).</li>
    <li><strong>Donations</strong> — confirm or reject submitted donations.</li>
    <li><strong>Newsletter</strong> — manage the subscriber list (view, remove).</li>
    <li><strong>Counseling Bookings</strong> — oversee bookings across every counselor; reassign to a different counselor or cancel if needed, independent of what the counselor themselves can do.</li>
</ul>

<h3>FrontEnd (Site Content)</h3>
<p><strong>Banners</strong>, <strong>Testimonials</strong>, <strong>Team Members</strong>, and <strong>Daily Ayah/Hadith</strong> control what appears on the public homepage/about page — all support drag-to-reorder and a publish/unpublish toggle. <strong>Blog Posts</strong> is shared with Content Managers (see their tab) — as an Admin you can see and edit everyone's posts, not just your own.</p>

<h3>🎓 Certificates</h3>
<p>Issue a certificate manually to any user for anything not covered by the automatic course-completion/Volunteer-ID certificates — Admin-role-only, since this can mint an arbitrary credential.</p>

<h3>⚙️ Settings</h3>
<p>Site-wide configuration, Admin-role-only:</p>
<ul>
    <li><strong>General</strong> — site name, SEO defaults (meta description/keywords, social share image).</li>
    <li><strong>Payment</strong> — JazzCash/Easypaisa numbers, bank account title/number/IBAN (encrypted at rest in the database), and the Nikah verification fee amount.</li>
    <li><strong>OAuth</strong> — Google/Facebook/TikTok login client ID and secret (secrets encrypted at rest).</li>
    <li><strong>Demo Data</strong> — generate or remove demo Nikah profiles for testing/demoing the app without touching real data.</li>
</ul>

<h3>🔗 Integrations</h3>
<p>Connect social accounts (Facebook, Twitter/X, YouTube, TikTok, Threads) used for Community Posts auto-cross-posting, and WhatsApp Business credentials used for WhatsApp notifications/broadcasts — Admin-role-only, since these hold live API credentials (also encrypted at rest). Retry a failed social post dispatch from here. Facebook has two separate connections by design — one for member login, one for posting — since they're different Meta App configurations.</p>

<h3>🌍 Localization</h3>
<p><strong>Languages</strong> controls which locales are available site-wide and which is default. <strong>Translations</strong> lets you edit any translatable UI string per-locale directly from the admin panel — no code deploy needed for wording changes.</p>

<h3>🖥️ System Maintenance</h3>
<p>One-click <strong>database optimization</strong> and <strong>image optimization</strong> (re-compresses stored images to save space) — safe to run anytime, most useful after a big import or cleanup.</p>

<h3>💬 Support Queries</h3>
<p>Family Support requests members submit when they need personal/sensitive help. Assign a query to a counselor/staff member, reply through the private thread, and update its status (New → Assigned → In Progress → Resolved/Closed).</p>

<h3>Security Notes</h3>
<p>Sensitive settings values (OAuth secrets, bank/JazzCash numbers) are encrypted in the database. Registration, login, and bulk-send actions are all rate-limited to prevent abuse. Every user-submitted rich text (blog posts, community posts, bulk email bodies) is sanitized before storage — pasted scripts or unsafe HTML never survive. Member-submitted Public Posts are stored as plain text (not rich HTML) specifically to keep that open-to-everyone submission surface as safe as possible.</p>

</div>

<div x-show="lang === 'ur'" x-cloak dir="rtl" class="prose prose-sm max-w-none prose-ur text-right">

<h3>ڈیش بورڈ</h3>
<p>آپ کا <strong>ایڈمن ڈیش بورڈ</strong> نقطہ آغاز ہے — ہر اسٹیٹ ٹائل براہ راست متعلقہ انتظامی صفحے سے منسلک ہے، شعبے کے لحاظ سے گروپ کیا گیا (یوزرز، نکاح، قرآن/اسکلز کورسز، قرآن لائیو کلاسز، رضاکار، عطیات، عوامی پوسٹس، سپورٹ)۔ سائیڈ بار میں کہیں بھی زرد/سرخ بیجز کا مطلب ہے کہ کوئی چیز آپ کے منتظر ہے۔</p>

<h3>👥 یوزرز اور اجازتیں</h3>
<ul>
    <li><strong>تمام یوزرز</strong> — کسی بھی اکاؤنٹ کی تفصیلات دیکھیں، ان کے پروفائل فیلڈز میں ترمیم کریں، انہیں پاس ورڈ ری سیٹ لنک بھیجیں، ان کا کردار بدلیں، اکاؤنٹ کو غیر فعال/دوبارہ فعال کریں (غیر فعال کرنے سے ان کی نکاح پروفائل بھی چھپ جاتی ہے مگر کوئی ڈیٹا حذف نہیں ہوتا)، یا اکاؤنٹ مکمل طور پر حذف کریں۔</li>
    <li><strong>کردار جائزہ</strong> — کسٹم کردار بنائیں اور مکمل ایڈمن کردار دیے بغیر اسٹاف کو محدود اجازتیں (دیکھنا/انتظام/حذف، فی وسیلہ) تفویض کریں۔ تفویض کے قابل وسائل: عوامی پوسٹس، کمیونٹی پوسٹس، نکاح، عطیات، رضاکار، فیملی سپورٹ، وال ماڈریشن، فیملی کاؤنسلنگ۔ کسی کو مثلاً <code>wall.manage</code> دیں تاکہ وہ یوزرز، سیٹنگز، یا انٹیگریشنز کو چھوئے بغیر کمیونٹی فیڈ کی نگرانی کر سکے۔</li>
    <li>چند انتہائی حساس شعبے <strong>ہمیشہ صرف ایڈمن کردار کے لیے</strong> ہیں، چاہے کوئی بھی اجازت دی گئی ہو: یوزرز، سیٹنگز، سرٹیفکیٹس، قرآن/اسکلز کورس مواد، قرآن لائیو کلاس کا انتظام، سبسکرائبرز، لوکلائزیشن، اور لائیو انٹیگریشن کریڈینشلز۔ یہ جان بوجھ کر ہے — یہ "ایک محدود اسٹاف کردار کو تفویض کریں" جیسے فیصلے نہیں ہیں۔</li>
</ul>

<h3>📢 اجتماعی پیغامات</h3>
<p>فلٹر شدہ یوزرز کے گروپ، یا نیوز لیٹر سبسکرائبرز (صرف ای میل) کو ای میل یا واٹس ایپ نشریات بھیجیں۔</p>
<ul>
    <li>لکھنے سے پہلے وصول کنندگان کو ماڈیول، کردار، شہر، شمولیت کی تاریخ، وغیرہ کے لحاظ سے فلٹر کریں۔</li>
    <li>ای میل کمپوزر ایک مکمل رچ ٹیکسٹ ایڈیٹر ہے (ورڈ/ڈاکس سے پیسٹ کریں) پہلے سے بنے ہوئے، اِن لائن CSS نیوز لیٹر ٹیمپلیٹس کے ساتھ جنہیں آپ لوڈ اور ترمیم کر سکتے ہیں — نیز ایک <strong>"+ نام شامل کریں"</strong> بٹن جو ایک <code>@{{name}}</code> ٹوکن شامل کرتا ہے، جو بھیجتے وقت خودکار طور پر ہر وصول کنندہ کے اصل نام سے بدل جاتا ہے۔</li>
    <li>دونوں چینلز روزانہ محفوظ بھیجنے کی حد (ہر ایک 500/دن — جی میل کی عملی حد اور واٹس ایپ کے لیے ایک محتاط حد کے مطابق، تاکہ بھیجنے والا نمبر/اکاؤنٹ فلیگ یا بین نہ ہو) کے اندر رہنے کے لیے خودکار طور پر تقسیم ہو جاتے ہیں۔ آج کی باقی گنجائش سے بڑی مہم خودکار طور پر تقسیم ہو جاتی ہے، باقی اگلے دن(دنوں) صبح 9 بجے کے لیے قطار میں لگ جاتی ہے — دستی تقسیم کی ضرورت نہیں۔</li>
    <li><strong>اجتماعی پیغامات</strong> کی تاریخ کا صفحہ اوپر ایک رپورٹ کارڈ دکھاتا ہے (کل مہمات، پہنچے گئے وصول کنندگان، ترسیل شدہ/ناکام، ترسیل کی شرح، آج استعمال شدہ گنجائش) اور فی مہم ترسیل کی حیثیت۔</li>
</ul>

<h3>💍 نکاح کا انتظام</h3>
<ul>
    <li><strong>لیڈز</strong> — میچ میکر سی آر ایم: کسی واٹس ایپ/فیس بک/انسٹاگرام رابطے کو حقیقی اکاؤنٹ بننے سے پہلے ہی درج کریں، حیثیت/فالو اپ/نوٹس ٹریک کریں، "کلائنٹ میں تبدیل کریں" کے ذریعے نیچے دیے گئے واک اِن عمل میں ان کا نام/رابطہ پہلے سے بھرا ہوا منتقل کریں، ان کے لیے امیدواروں کی شارٹ لسٹ بنائیں، اور ایک سادہ میچ میکنگ پیکج + قیمت ٹریک کریں۔ مکمل ایڈمن ہر میچ میکر کی لیڈز دیکھتے ہیں؛ ایک میچ میکر صرف اپنی لیڈز دیکھتا ہے۔</li>
    <li><strong>تمام پروفائلز</strong> / <strong>تصدیقات</strong> — نئی پروفائلز (شناختی کارڈ نمبر + آگے/پیچھے کی تصاویر) کا جائزہ لیں، ایک وجہ کے ساتھ منظور یا مسترد کریں۔ سیدھے معاملات کے لیے اجتماعی منظوری دستیاب ہے۔ نامکمل رہ جانے والی پروفائلز کو یاد دہانی بھیجیں۔</li>
    <li><strong>ادائیگیاں</strong> — پروفائل لائیو ہونے سے پہلے یک بارگی تصدیقی فیس کی تصدیق یا مسترد کریں؛ آپ دستی طور پر آف لائن ادائیگی بھی درج کر سکتے ہیں۔</li>
    <li><strong>رپورٹس</strong> — وہ حفاظتی رپورٹس جو ممبران ایک دوسرے کے خلاف جمع کرواتے ہیں؛ رپورٹ سے منسلک گفتگو کی تاریخ (اگر کوئی ہو) دیکھیں اور اگر جائز ہو تو پروفائل کو معطل کریں۔ ان رپورٹس کو خارج کریں جن پر کارروائی کی ضرورت نہیں۔</li>
    <li><strong>رابطہ درخواستیں</strong> — میچ میکر کی جانب سے شروع کردہ تعارف جو آپ کی منظوری/مسترد کرنے کے فیصلے کے منتظر ہیں۔</li>
    <li><strong>پروفائل بنائیں</strong> — وہی واک اِن عمل جو میچ میکرز استعمال کرتے ہیں، اسٹاف کی مدد سے پروفائل بنانے کے لیے۔</li>
    <li><strong>سرپرست کی تصدیق</strong> — پروفائل پر اضافی اعتماد کے اشارے کے لیے سرپرست کی شناخت/رابطے کو تصدیق شدہ نشان زد کریں۔</li>
</ul>

<h3>📖 قرآن اور 💻 ڈیجیٹل اسکلز کورسز</h3>
<p><strong>کورسز</strong> کے تحت کورسز، اسباق، اور کوئزز بنائیں/ترمیم کریں — اوپر موجود پِل سوئچ سے ٹریک (قرآن بمقابلہ اسکلز) کے لحاظ سے فلٹر کریں؛ ہر کورس کی قطار ایک ٹریک بیج دکھاتی ہے۔ کسی کورس کے ممبران کے لیے بامعنی طور پر مفید ہونے کے لیے کم از کم ایک شائع شدہ سبق (اور ترجیحاً ایک کوئز) درکار ہے۔ اسباق تحریری اور ویڈیو مواد کی حمایت کرتے ہیں؛ کوئزز کثیر انتخابی ہوتے ہیں جن میں پاس ہونے کی حد مقرر ہوتی ہے۔</p>

<h3>🎥 قرآن لائیو کلاسز</h3>
<ul>
    <li><strong>کورس درجات</strong> (قرآن لائیو کورسز) — وہ درجات جن میں طلبہ داخل ہوتے ہیں (مثلاً "درجہ 1 ناظرہ")، ہر ایک کی اپنی ماہانہ فیس۔</li>
    <li><strong>کلاس گروپس</strong> — ہر درجے کے لیے داخل شدہ طلبہ کو ایک استاد کی زیرِ قیادت گروپ میں تفویض کریں؛ کسی استاد کو تفویض کرنے سے پہلے اس کی جانچ/منظوری ضروری ہے۔</li>
    <li><strong>داخلے</strong> — زیرِ التوا داخلوں کا جائزہ لیں اور ہر ایک کو کسی گروپ میں تفویض کریں، یا ایک وجہ کے ساتھ مسترد کریں۔</li>
    <li><strong>سبسکرپشنز</strong> — ہر طالب علم کی ماہانہ ادائیگی جمع کرائی گئی (اسکرین شاٹ + حوالہ نمبر) کی تصدیق یا مسترد کریں۔</li>
</ul>

<h3>کمیونٹی</h3>
<ul>
    <li><strong>سلامتی وال</strong> — ممبران کی جانب سے جمع کروائی گئی دعا کی درخواستوں کو منظور یا چھپائیں (چھپانے کی صورت میں ایک وجہ کے ساتھ)؛ حیثیت کے لحاظ سے فلٹر کریں (زیرِ التوا/منظور شدہ/چھپی ہوئی/تمام)۔</li>
    <li><strong>کمیونٹی پوسٹس</strong> — وال کے لیے آپ کا اپنا ایڈمن تحریری مواد (سرگرمیاں، تقریبات، خطبات)، کسی بھی منسلک سوشل اکاؤنٹ (فیس بک/ٹویٹر/یوٹیوب/ٹک ٹاک/تھریڈز — انٹیگریشنز دیکھیں) پر اختیاری خودکار کراس پوسٹنگ کے ساتھ، ایک اجتماعی تصویر/ویڈیو اپ لوڈ ٹول جو پرانا میڈیا شیڈول شدہ قطار میں بدل دیتا ہے، ڈریگ سے دوبارہ ترتیب دینے کی شیڈولنگ، اور پن کر کے پوسٹ کو وال کے اوپر لے جانا۔</li>
    <li><strong>عوامی پوسٹس</strong> — ممبر/اسٹاف کی جمع کردہ کہانیاں جو جائزے کی منتظر ہیں، ایک اعداد و شمار کی صف کے ساتھ (زیرِ التوا/شائع شدہ/مسترد/کل)۔ فوراً شائع کرنے کے لیے منظور کریں (مصنف کو مطلع کرتا ہے، اور پوسٹ اپنے عوامی URL پر لائیو ہو جاتی ہے)؛ ایک وجہ کے ساتھ مسترد کریں (مصنف کو بھی مطلع کرتا ہے)۔ جو کوئی <code>posts.manage</code> رکھتا ہے اس کی اپنی جمع کردہ پوسٹس بغیر جائزے کے خودکار شائع ہو جاتی ہیں۔</li>
    <li><strong>رضاکار</strong> — منظور کریں (رجسٹرڈ درخواست گزاروں کے لیے خودکار طور پر رضاکار کردار تفویض کرتا ہے اور رضاکار شناختی کارڈ PDF ای میل کرتا ہے؛ مہمان درخواست گزاروں کو رجسٹر ہونے کی دعوت دیتے ہوئے ای میل کرتا ہے تاکہ ان کا شناختی کارڈ جاری کیا جا سکے) یا مسترد کریں (رجسٹرڈ ہو یا مہمان، دونوں صورتوں میں درخواست گزار کو مطلع کیا جاتا ہے)۔</li>
    <li><strong>عطیات</strong> — جمع کرائے گئے عطیات کی تصدیق یا مسترد کریں۔</li>
    <li><strong>نیوز لیٹر</strong> — سبسکرائبر لسٹ کا انتظام کریں (دیکھیں، ہٹائیں)۔</li>
    <li><strong>کاؤنسلنگ بکنگز</strong> — ہر کاؤنسلر کی بکنگز کی نگرانی کریں؛ اگر ضرورت ہو تو کسی دوسرے کاؤنسلر کو دوبارہ تفویض کریں یا منسوخ کریں، خود کاؤنسلر کے اختیارات سے آزادانہ طور پر۔</li>
</ul>

<h3>فرنٹ اینڈ (سائٹ مواد)</h3>
<p><strong>بینرز</strong>، <strong>تعریفی آراء</strong>، <strong>ٹیم ممبرز</strong>، اور <strong>روزانہ آیت/حدیث</strong> عوامی ہوم پیج/ابوٹ پیج پر نظر آنے والی چیزوں کو کنٹرول کرتے ہیں — سب ڈریگ سے دوبارہ ترتیب دینے اور شائع/غیر شائع کے ٹوگل کی حمایت کرتے ہیں۔ <strong>بلاگ پوسٹس</strong> مواد مینیجرز کے ساتھ مشترک ہے (ان کا ٹیب دیکھیں) — ایک ایڈمن کے طور پر آپ سب کی پوسٹس دیکھ اور ترمیم کر سکتے ہیں، صرف اپنی نہیں۔</p>

<h3>🎓 سرٹیفکیٹس</h3>
<p>خودکار کورس تکمیل/رضاکار شناختی کارڈ سرٹیفکیٹس کے علاوہ کسی بھی چیز کے لیے کسی بھی یوزر کو دستی طور پر سرٹیفکیٹ جاری کریں — صرف ایڈمن کردار کے لیے، کیونکہ یہ ایک من مانی سند بنا سکتا ہے۔</p>

<h3>⚙️ سیٹنگز</h3>
<p>سائٹ بھر کی ترتیبات، صرف ایڈمن کردار کے لیے:</p>
<ul>
    <li><strong>عمومی</strong> — سائٹ کا نام، SEO ڈیفالٹس (میٹا تفصیل/کی ورڈز، سوشل شیئر تصویر)۔</li>
    <li><strong>ادائیگی</strong> — جیز کیش/ایزی پیسہ نمبرز، بینک اکاؤنٹ کا عنوان/نمبر/IBAN (ڈیٹا بیس میں انکرپٹڈ)، اور نکاح تصدیقی فیس کی رقم۔</li>
    <li><strong>OAuth</strong> — گوگل/فیس بک/ٹک ٹاک لاگ اِن کلائنٹ ID اور سیکرٹ (سیکرٹس انکرپٹڈ)۔</li>
    <li><strong>ڈیمو ڈیٹا</strong> — حقیقی ڈیٹا کو چھوئے بغیر ایپ کی جانچ/ڈیمو کے لیے ڈیمو نکاح پروفائلز بنائیں یا ہٹائیں۔</li>
</ul>

<h3>🔗 انٹیگریشنز</h3>
<p>کمیونٹی پوسٹس کی خودکار کراس پوسٹنگ کے لیے استعمال ہونے والے سوشل اکاؤنٹس (فیس بک، ٹویٹر/X، یوٹیوب، ٹک ٹاک، تھریڈز) کو منسلک کریں، اور واٹس ایپ اطلاعات/نشریات کے لیے واٹس ایپ بزنس کریڈینشلز — صرف ایڈمن کردار کے لیے، کیونکہ ان میں لائیو API کریڈینشلز ہوتے ہیں (یہ بھی انکرپٹڈ)۔ یہاں سے ناکام سوشل پوسٹ ترسیل کو دوبارہ کوشش کریں۔ فیس بک کے جان بوجھ کر دو الگ کنکشنز ہیں — ایک ممبر لاگ اِن کے لیے، ایک پوسٹنگ کے لیے — کیونکہ یہ مختلف Meta ایپ کنفیگریشنز ہیں۔</p>

<h3>🌍 لوکلائزیشن</h3>
<p><strong>زبانیں</strong> کنٹرول کرتی ہیں کہ سائٹ بھر میں کون سی زبانیں دستیاب ہیں اور کون سی ڈیفالٹ ہے۔ <strong>ترجمے</strong> آپ کو ایڈمن پینل سے براہ راست فی زبان کوئی بھی قابلِ ترجمہ UI سٹرنگ ترمیم کرنے دیتے ہیں — الفاظ کی تبدیلی کے لیے کوڈ ڈیپلائے کی ضرورت نہیں۔</p>

<h3>🖥️ سسٹم مینٹیننس</h3>
<p>ایک کلک پر <strong>ڈیٹا بیس کی بہتری</strong> اور <strong>تصویر کی بہتری</strong> (جگہ بچانے کے لیے محفوظ شدہ تصاویر کو دوبارہ کمپریس کرتا ہے) — کسی بھی وقت چلانا محفوظ ہے، بڑی درآمد یا صفائی کے بعد سب سے زیادہ مفید۔</p>

<h3>💬 سپورٹ کوئریز</h3>
<p>فیملی سپورٹ کی درخواستیں جو ممبران ذاتی/حساس مدد درکار ہونے پر جمع کرواتے ہیں۔ کسی کوئری کو کاؤنسلر/اسٹاف ممبر کو تفویض کریں، نجی سلسلے کے ذریعے جواب دیں، اور اس کی حیثیت اپڈیٹ کریں (نیا → تفویض شدہ → جاری → حل شدہ/بند)۔</p>

<h3>حفاظتی نوٹس</h3>
<p>حساس سیٹنگز ویلیوز (OAuth سیکرٹس، بینک/جیز کیش نمبرز) ڈیٹا بیس میں انکرپٹڈ ہیں۔ رجسٹریشن، لاگ اِن، اور اجتماعی بھیجنے کی کارروائیاں سب زیادتی روکنے کے لیے شرح محدود ہیں۔ ہر یوزر کی جمع کردہ رچ ٹیکسٹ (بلاگ پوسٹس، کمیونٹی پوسٹس، اجتماعی ای میل باڈیز) محفوظ کرنے سے پہلے صاف کی جاتی ہے — پیسٹ شدہ اسکرپٹس یا غیر محفوظ HTML کبھی باقی نہیں رہتے۔ ممبر کی جمع کردہ عوامی پوسٹس خاص طور پر عام متن (رچ HTML نہیں) کے طور پر محفوظ کی جاتی ہیں تاکہ یہ سب کے لیے کھلی جمع کروانے کی سطح ممکنہ حد تک محفوظ رہے۔</p>

</div>
