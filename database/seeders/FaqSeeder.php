<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // General
            ['module' => 'general', 'sort_order' => 1,
                'question_en' => 'How do I create an account on Sallaamti?',
                'answer_en' => "Tap \"Register\" at the top of the page, enter your name and email or WhatsApp number, choose a password, and select which programs you're interested in (Quran, Nikah, Digital Skills, Counseling). You'll receive a verification code — enter it to activate your account.",
                'question_ur' => 'سلامتی پر اکاؤنٹ کیسے بنائیں؟',
                'answer_ur' => 'صفحہ کے اوپر "رجسٹر" پر کلک کریں، اپنا نام اور ای میل یا واٹس ایپ نمبر درج کریں، پاسورڈ منتخب کریں، اور وہ پروگرام منتخب کریں جن میں آپ کی دلچسپی ہے (قرآن، نکاح، ڈیجیٹل سکلز، کاؤنسلنگ)۔ آپ کو ایک تصدیقی کوڈ موصول ہوگا — اپنا اکاؤنٹ فعال کرنے کے لیے وہ کوڈ درج کریں۔'],
            ['module' => 'general', 'sort_order' => 2,
                'question_en' => 'I forgot my password, what do I do?',
                'answer_en' => 'On the login page, tap "Forgot password?", enter your email or WhatsApp number, and follow the instructions sent to you to set a new password.',
                'question_ur' => 'میں اپنا پاسورڈ بھول گیا ہوں، کیا کروں؟',
                'answer_ur' => 'لاگ ان صفحہ پر "پاسورڈ بھول گئے؟" پر کلک کریں، اپنا ای میل یا واٹس ایپ نمبر درج کریں، اور نیا پاسورڈ بنانے کے لیے بھیجی گئی ہدایات پر عمل کریں۔'],
            ['module' => 'general', 'sort_order' => 3,
                'question_en' => 'Is Sallaamti free to use?',
                'answer_en' => 'Yes — registration, browsing courses, and most features are free. Some services, like Nikah profile verification, may have a small one-time fee, which is always shown clearly before you pay.',
                'question_ur' => 'کیا سلامتی استعمال کرنا مفت ہے؟',
                'answer_ur' => 'جی ہاں — رجسٹریشن، کورسز دیکھنا، اور زیادہ تر سہولیات مفت ہیں۔ کچھ خدمات، جیسے نکاح پروفائل کی تصدیق، پر معمولی یک وقتی فیس ہو سکتی ہے، جو ادائیگی سے پہلے واضح طور پر دکھائی جاتی ہے۔'],
            ['module' => 'general', 'sort_order' => 4,
                'question_en' => "I can't understand English, can I use Sallaamti in Urdu?",
                'answer_en' => 'Yes — tap the language icon at the top of any page and choose Urdu. Most of the site, including these FAQs, is available in Urdu.',
                'question_ur' => 'مجھے انگریزی سمجھ نہیں آتی، کیا میں سلامتی اردو میں استعمال کر سکتا ہوں؟',
                'answer_ur' => 'جی ہاں — کسی بھی صفحے کے اوپر زبان کا آئیکن دبائیں اور اردو منتخب کریں۔ ویب سائٹ کا زیادہ تر حصہ، بشمول یہ سوالات، اردو میں دستیاب ہے۔'],

            // Nikah
            ['module' => 'nikah', 'sort_order' => 1,
                'question_en' => 'How do I create a Nikah profile?',
                'answer_en' => 'After logging in, go to the Nikah section and tap "Create Profile". Fill in your basic details, family background, religious practice, and what you\'re looking for in a spouse, step by step. At the end, you\'ll upload a photo and your CNIC for verification, then submit for review.',
                'question_ur' => 'نکاح پروفائل کیسے بنائیں؟',
                'answer_ur' => 'لاگ ان کرنے کے بعد، نکاح سیکشن میں جائیں اور "پروفائل بنائیں" پر کلک کریں۔ اپنی بنیادی معلومات، خاندانی پس منظر، دینی رجحان، اور آپ اپنے ہمسفر میں کیا تلاش کر رہے ہیں، مرحلہ وار پُر کریں۔ آخر میں، آپ اپنی تصویر اور شناختی کارڈ تصدیق کے لیے اپ لوڈ کریں گے، پھر جائزے کے لیے جمع کروائیں۔'],
            ['module' => 'nikah', 'sort_order' => 2,
                'question_en' => 'Why does my Nikah profile need verification?',
                'answer_en' => 'Verification protects everyone on the platform from fake profiles. Our team checks your CNIC and details before your profile becomes visible to other verified members — this usually takes 1-3 days.',
                'question_ur' => 'میری نکاح پروفائل کو تصدیق کی ضرورت کیوں ہے؟',
                'answer_ur' => 'تصدیق پلیٹ فارم پر موجود ہر فرد کو جعلی پروفائلز سے محفوظ رکھتی ہے۔ ہماری ٹیم آپ کی پروفائل دوسرے تصدیق شدہ اراکین کو نظر آنے سے پہلے آپ کا شناختی کارڈ اور تفصیلات چیک کرتی ہے — اس میں عام طور پر 1 سے 3 دن لگتے ہیں۔'],
            ['module' => 'nikah', 'sort_order' => 3,
                'question_en' => 'Is there a fee for the Nikah service?',
                'answer_en' => 'Browsing and creating a profile is free. A one-time, non-refundable verification fee applies once your profile is reviewed — the exact amount is shown on your profile before payment.',
                'question_ur' => 'کیا نکاح سروس کے لیے کوئی فیس ہے؟',
                'answer_ur' => 'پروفائل بنانا اور دیکھنا مفت ہے۔ آپ کی پروفائل کے جائزے کے بعد ایک یک وقتی، ناقابلِ واپسی تصدیقی فیس لاگو ہوتی ہے — درست رقم ادائیگی سے پہلے آپ کی پروفائل پر دکھائی جاتی ہے۔'],
            ['module' => 'nikah', 'sort_order' => 4,
                'question_en' => 'Who can see my Nikah profile?',
                'answer_en' => 'Only verified members can browse profiles. You control what details are visible, and your CNIC and full contact information are never shown publicly.',
                'question_ur' => 'میری نکاح پروفائل کون دیکھ سکتا ہے؟',
                'answer_ur' => 'صرف تصدیق شدہ اراکین ہی پروفائلز دیکھ سکتے ہیں۔ آپ کنٹرول کرتے ہیں کہ کون سی تفصیلات نظر آئیں، اور آپ کا شناختی کارڈ اور مکمل رابطہ معلومات کبھی بھی عوامی طور پر ظاہر نہیں کی جاتیں۔'],
            ['module' => 'nikah', 'sort_order' => 5,
                'question_en' => 'How do I contact someone whose profile I like?',
                'answer_en' => 'Tap "Send Interest" on their profile. If they accept, you can then message each other within the platform. We recommend involving your family/wali before proceeding further.',
                'question_ur' => 'مجھے جس کی پروفائل پسند ہے اس سے رابطہ کیسے کروں؟',
                'answer_ur' => 'ان کی پروفائل پر "دلچسپی بھیجیں" پر کلک کریں۔ اگر وہ قبول کرلیں، تو آپ پلیٹ فارم کے اندر ایک دوسرے کو پیغام بھیج سکتے ہیں۔ ہم آگے بڑھنے سے پہلے اپنے خاندان/ولی کو شامل کرنے کی سفارش کرتے ہیں۔'],

            // Quran self-paced
            ['module' => 'quran', 'sort_order' => 1,
                'question_en' => 'How do I enroll in a Quran course?',
                'answer_en' => 'Browse the Quran Courses page, tap a course, then tap "Enroll". You can start learning immediately at your own pace.',
                'question_ur' => 'میں قرآن کورس میں کیسے داخلہ لوں؟',
                'answer_ur' => 'قرآن کورسز کا صفحہ دیکھیں، کسی کورس پر کلک کریں، پھر "داخلہ لیں" پر کلک کریں۔ آپ فوراً اپنی رفتار سے سیکھنا شروع کر سکتے ہیں۔'],
            ['module' => 'quran', 'sort_order' => 2,
                'question_en' => 'Do I get a certificate after finishing a course?',
                'answer_en' => 'Yes — once you complete all lessons and pass the quiz, a certificate is automatically issued to your account, which you can download or share.',
                'question_ur' => 'کورس مکمل کرنے کے بعد کیا مجھے سرٹیفکیٹ ملے گا؟',
                'answer_ur' => 'جی ہاں — تمام اسباق مکمل کرنے اور کوئز پاس کرنے کے بعد، آپ کے اکاؤنٹ میں خودکار طور پر ایک سرٹیفکیٹ جاری کیا جاتا ہے، جسے آپ ڈاؤن لوڈ یا شیئر کر سکتے ہیں۔'],
            ['module' => 'quran', 'sort_order' => 3,
                'question_en' => 'Can I learn at my own pace?',
                'answer_en' => 'Yes, self-paced Quran courses have no deadlines — you can pause and resume anytime, and your progress is saved automatically.',
                'question_ur' => 'کیا میں اپنی رفتار سے سیکھ سکتا ہوں؟',
                'answer_ur' => 'جی ہاں، سیلف پیسڈ قرآن کورسز کی کوئی ڈیڈ لائن نہیں ہوتی — آپ کسی بھی وقت روک اور دوبارہ شروع کر سکتے ہیں، اور آپ کی پیش رفت خودکار طور پر محفوظ ہو جاتی ہے۔'],

            // Live Quran Classes
            ['module' => 'quran_live', 'sort_order' => 1,
                'question_en' => 'How is Live Quran Classes different from the self-paced courses?',
                'answer_en' => 'Live classes are taught by a teacher in real time over video call, on a fixed weekly schedule, and are great for children or anyone who prefers direct guidance.',
                'question_ur' => 'لائیو قرآن کلاسز سیلف پیسڈ کورسز سے کیسے مختلف ہیں؟',
                'answer_ur' => 'لائیو کلاسز ایک استاد کی جانب سے حقیقی وقت میں ویڈیو کال پر، مقررہ ہفتہ وار شیڈول پر پڑھائی جاتی ہیں، اور بچوں یا براہ راست رہنمائی چاہنے والوں کے لیے بہترین ہیں۔'],
            ['module' => 'quran_live', 'sort_order' => 2,
                'question_en' => 'How do I admit my child to Live Quran Classes?',
                'answer_en' => 'Go to Live Quran Classes and tap "Admission". Fill in the parent\'s contact details, information about the student, and your preferred class timing, then submit — our team will confirm your class group.',
                'question_ur' => 'میں اپنے بچے کو لائیو قرآن کلاسز میں کیسے داخل کروں؟',
                'answer_ur' => 'لائیو قرآن کلاسز پر جائیں اور "داخلہ" پر کلک کریں۔ والدین کی رابطہ تفصیلات، طالب علم کی معلومات، اور اپنی پسندیدہ کلاس ٹائمنگ درج کریں، پھر جمع کروائیں — ہماری ٹیم آپ کا کلاس گروپ طے کرے گی۔'],
            ['module' => 'quran_live', 'sort_order' => 3,
                'question_en' => 'What if my child misses a class?',
                'answer_en' => 'Recordings or catch-up guidance are shared with the class group; contact your teacher through the in-app messaging to arrange a makeup where possible.',
                'question_ur' => 'اگر میرے بچے کی کلاس چھوٹ جائے تو؟',
                'answer_ur' => 'کلاس گروپ کے ساتھ ریکارڈنگ یا نظرثانی رہنمائی شیئر کی جاتی ہے؛ ممکنہ حد تک متبادل وقت کے لیے ایپ کے اندر میسجنگ کے ذریعے اپنے استاد سے رابطہ کریں۔'],

            // Digital Skills
            ['module' => 'skills', 'sort_order' => 1,
                'question_en' => 'What is Digital Skills training?',
                'answer_en' => 'Free courses to help you learn practical computer, internet, and freelancing skills, presented in partnership with IZMA Digital Technology & Security.',
                'question_ur' => 'ڈیجیٹل سکلز ٹریننگ کیا ہے؟',
                'answer_ur' => 'یہ مفت کورسز ہیں جو آپ کو کمپیوٹر، انٹرنیٹ، اور فری لانسنگ کی عملی مہارتیں سیکھنے میں مدد دیتے ہیں، جو IZMA ڈیجیٹل ٹیکنالوجی اینڈ سیکیورٹی کے تعاون سے پیش کیے جاتے ہیں۔'],
            ['module' => 'skills', 'sort_order' => 2,
                'question_en' => 'Do I need any prior computer knowledge?',
                'answer_en' => 'No — courses are designed to start from the basics, so complete beginners are welcome.',
                'question_ur' => 'کیا مجھے پہلے سے کمپیوٹر کا علم ہونا ضروری ہے؟',
                'answer_ur' => 'نہیں — کورسز بنیادی سطح سے شروع کرنے کے لیے بنائے گئے ہیں، اس لیے مکمل نوآموز بھی خوش آمدید ہیں۔'],

            // Counseling
            ['module' => 'counseling', 'sort_order' => 1,
                'question_en' => 'How do I book a family counseling session?',
                'answer_en' => 'Go to Family Counseling, tap "Book a Session", choose the category of your concern and a preferred time, and submit — a counselor will confirm your appointment.',
                'question_ur' => 'میں فیملی کاؤنسلنگ سیشن کیسے بک کروں؟',
                'answer_ur' => 'فیملی کاؤنسلنگ پر جائیں، "سیشن بک کریں" پر کلک کریں، اپنے مسئلے کی قسم اور پسندیدہ وقت منتخب کریں، اور جمع کروائیں — ایک کاؤنسلر آپ کی اپائنٹمنٹ کی تصدیق کرے گا۔'],
            ['module' => 'counseling', 'sort_order' => 2,
                'question_en' => 'Is what I share with a counselor kept private?',
                'answer_en' => 'Yes, everything you share is kept confidential and used only to support your session.',
                'question_ur' => 'کیا کاؤنسلر کے ساتھ شیئر کی گئی معلومات خفیہ رہتی ہیں؟',
                'answer_ur' => 'جی ہاں، آپ کی شیئر کردہ ہر معلومات خفیہ رکھی جاتی ہے اور صرف آپ کے سیشن کی مدد کے لیے استعمال ہوتی ہے۔'],

            // Donation
            ['module' => 'donation', 'sort_order' => 1,
                'question_en' => 'How do I make a donation?',
                'answer_en' => 'Go to the Donate page, choose an amount and cause, then pay via JazzCash, EasyPaisa, or bank transfer and submit your payment reference. Our team verifies it shortly after.',
                'question_ur' => 'میں عطیہ کیسے دوں؟',
                'answer_ur' => 'عطیہ کے صفحے پر جائیں، رقم اور مقصد منتخب کریں، پھر جاز کیش، ایزی پیسہ، یا بینک ٹرانسفر کے ذریعے ادائیگی کریں اور اپنا ادائیگی حوالہ جمع کروائیں۔ ہماری ٹیم جلد اس کی تصدیق کرے گی۔'],
            ['module' => 'donation', 'sort_order' => 2,
                'question_en' => 'Do I need an account to donate?',
                'answer_en' => 'No, you can donate as a guest — but creating an account lets you track your donation history.',
                'question_ur' => 'کیا عطیہ دینے کے لیے اکاؤنٹ ضروری ہے؟',
                'answer_ur' => 'نہیں، آپ بغیر اکاؤنٹ کے بھی عطیہ دے سکتے ہیں — لیکن اکاؤنٹ بنانے سے آپ اپنی عطیات کی تاریخ دیکھ سکتے ہیں۔'],

            // Volunteer
            ['module' => 'volunteer', 'sort_order' => 1,
                'question_en' => 'How do I become a volunteer?',
                'answer_en' => 'Go to the Volunteer page, fill in your details and area of interest, and submit. Our team reviews applications and will contact you once approved.',
                'question_ur' => 'میں رضاکار کیسے بنوں؟',
                'answer_ur' => 'رضاکار کے صفحے پر جائیں، اپنی تفصیلات اور دلچسپی کا شعبہ درج کریں، اور جمع کروائیں۔ ہماری ٹیم درخواستوں کا جائزہ لیتی ہے اور منظوری کے بعد آپ سے رابطہ کرے گی۔'],
            ['module' => 'volunteer', 'sort_order' => 2,
                'question_en' => 'What do I get as an approved volunteer?',
                'answer_en' => 'You receive an official Sallaamti Volunteer ID card, valid while your volunteer status is active.',
                'question_ur' => 'منظور شدہ رضاکار کے طور پر مجھے کیا ملتا ہے؟',
                'answer_ur' => 'آپ کو سلامتی کا سرکاری رضاکار شناختی کارڈ ملتا ہے، جو آپ کی رضاکارانہ حیثیت فعال رہنے تک درست رہتا ہے۔'],

            // Wall
            ['module' => 'wall', 'sort_order' => 1,
                'question_en' => 'What can I post on the Sallaamti Wall?',
                'answer_en' => 'You can share dua requests, or a Community Post about an activity, achievement, or reminder. All posts are reviewed by our team before appearing publicly.',
                'question_ur' => 'میں سلامتی وال پر کیا پوسٹ کر سکتا ہوں؟',
                'answer_ur' => 'آپ دعا کی درخواست، یا کسی سرگرمی، کامیابی، یا نصیحت کے بارے میں کمیونٹی پوسٹ شیئر کر سکتے ہیں۔ تمام پوسٹس عوامی طور پر ظاہر ہونے سے پہلے ہماری ٹیم کے جائزے سے گزرتی ہیں۔'],
            ['module' => 'wall', 'sort_order' => 2,
                'question_en' => "Why isn't my post showing yet?",
                'answer_en' => 'Posts are reviewed by our team before publishing to keep the Wall safe and respectful — this usually takes a short while.',
                'question_ur' => 'میری پوسٹ ابھی تک کیوں نظر نہیں آ رہی؟',
                'answer_ur' => 'وال کو محفوظ اور باوقار رکھنے کے لیے پوسٹس شائع ہونے سے پہلے ہماری ٹیم کے جائزے سے گزرتی ہیں — اس میں عام طور پر تھوڑا وقت لگتا ہے۔'],
        ];

        foreach ($faqs as $f) {
            $f['is_active'] = true;
            Faq::firstOrCreate(
                ['module' => $f['module'], 'question_en' => $f['question_en']],
                $f
            );
        }
    }
}
