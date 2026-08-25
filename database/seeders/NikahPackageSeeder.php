<?php

namespace Database\Seeders;

use App\Models\NikahPackage;
use Illuminate\Database\Seeder;

// The initial three launch packages, plus a fourth (VIP) seeded inactive
// and hidden from the public page — ready for an admin to switch on later
// once there's a track record to justify the price, without needing a
// developer or a new migration. Every field here is editable afterward
// from admin > Nikah Packages; this only sets sensible starting values.
class NikahPackageSeeder extends Seeder
{
    public function run(): void
    {
        NikahPackage::updateOrCreate(['slug' => 'verified'], [
            'name' => 'Sallaamti Verified',
            'name_ur' => 'سلامتی تصدیق شدہ',
            'tagline' => 'Create and verify your own profile',
            'tagline_ur' => 'اپنی پروفائل خود بنائیں اور تصدیق کروائیں',
            'price' => 1000,
            'duration_days' => null,
            'proposal_limit' => null,
            'consultant_level' => 'Basic onboarding only',
            'description' => 'For people who want to use the platform themselves — registration, profile creation, verification, and full access to normal Sallaamti Nikah features. Consultant involvement is limited to basic onboarding, not personalized matchmaking.',
            'description_ur' => 'ان لوگوں کے لیے جو خود پلیٹ فارم استعمال کرنا چاہتے ہیں — رجسٹریشن، پروفائل بنانا، تصدیق، اور سلامتی نکاح کی تمام عام سہولیات تک مکمل رسائی۔ کنسلٹنٹ کی شمولیت صرف بنیادی رہنمائی تک محدود ہے، ذاتی میچ میکنگ تک نہیں۔',
            'features' => [
                'Registration & profile creation',
                'Profile verification',
                'Verified badge',
                'Profile activation',
                'Full access to normal Sallaamti Nikah features',
                'Basic support',
            ],
            'features_ur' => [
                'رجسٹریشن اور پروفائل بنانا',
                'پروفائل کی تصدیق',
                'تصدیق شدہ بیج',
                'پروفائل ایکٹیویشن',
                'سلامتی نکاح کی تمام عام سہولیات تک مکمل رسائی',
                'بنیادی سپورٹ',
            ],
            'color' => 'green',
            'icon' => '🟢',
            'sort_order' => 1,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        NikahPackage::updateOrCreate(['slug' => 'assisted-matchmaking'], [
            'name' => 'Assisted Matchmaking',
            'name_ur' => 'معاون میچ میکنگ',
            'tagline' => 'Guided matchmaking with a consultant\'s help',
            'tagline_ur' => 'کنسلٹنٹ کی مدد سے رہنمائی شدہ میچ میکنگ',
            'price' => 5000,
            'duration_days' => 90,
            'proposal_limit' => 15,
            'consultant_level' => 'Assisted',
            'description' => 'Our main matchmaking package — a consultant works with you on your requirements and actively searches on your behalf. Up to 5 suitable proposals are reviewed and shared per month (up to 15 over 3 months), based on database availability and compatibility. This is a cap on proposals shared, not a guarantee of matches.',
            'description_ur' => 'ہماری اہم میچ میکنگ سروس — ایک کنسلٹنٹ آپ کی ضروریات پر آپ کے ساتھ کام کرتا ہے اور آپ کی جانب سے فعال طور پر تلاش کرتا ہے۔ ڈیٹا بیس کی دستیابی اور مطابقت کی بنیاد پر ماہانہ 5 تک موزوں پروپوزلز کا جائزہ لے کر شیئر کیے جاتے ہیں (3 ماہ میں 15 تک)۔ یہ شیئر کیے گئے پروپوزلز کی حد ہے، رشتے کی ضمانت نہیں۔',
            'features' => [
                'Everything in Sallaamti Verified',
                'Female/male consultant assistance as appropriate',
                'Requirement consultation',
                'Profile optimization',
                'Search according to your requirements',
                'Up to 15 suitable proposals reviewed & shortlisted over 3 months (subject to availability & compatibility)',
                'Follow-up on mutual interest',
                'WhatsApp support',
                'Monthly profile review',
                'Guidance regarding family involvement',
            ],
            'features_ur' => [
                'سلامتی تصدیق شدہ کی تمام سہولیات',
                'حسبِ ضرورت خاتون/مرد کنسلٹنٹ کی معاونت',
                'ضروریات سے متعلق مشاورت',
                'پروفائل کی بہتری',
                'آپ کی ضروریات کے مطابق تلاش',
                '3 ماہ میں 15 تک موزوں پروپوزلز کا جائزہ اور شارٹ لسٹ (دستیابی اور مطابقت سے مشروط)',
                'باہمی دلچسپی پر پیروی',
                'واٹس ایپ سپورٹ',
                'ماہانہ پروفائل جائزہ',
                'خاندانی شمولیت سے متعلق رہنمائی',
            ],
            'color' => 'amber',
            'icon' => '🟡',
            'sort_order' => 2,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        NikahPackage::updateOrCreate(['slug' => 'premium-personal-matchmaking'], [
            'name' => 'Premium Personal Matchmaking',
            'name_ur' => 'پریمیئم پرسنل میچ میکنگ',
            'tagline' => '"I don\'t have time — you manage everything."',
            'tagline_ur' => '"میرے پاس وقت نہیں — آپ سب کچھ سنبھالیں۔"',
            'price' => 12000,
            'duration_days' => 90,
            'proposal_limit' => 30,
            'consultant_level' => 'Dedicated',
            'description' => 'A dedicated consultant manages your search end to end, from a detailed requirement interview to weekly progress updates. Up to 10 curated proposals are reviewed per month (up to 30 over 3 months), based on database availability and compatibility — a cap on proposals reviewed, not a guarantee of matches.',
            'description_ur' => 'ایک مخصوص کنسلٹنٹ تفصیلی ضروریات کے انٹرویو سے لے کر ہفتہ وار پیش رفت رپورٹس تک، آپ کی تلاش کا مکمل انتظام کرتا ہے۔ ڈیٹا بیس کی دستیابی اور مطابقت کی بنیاد پر ماہانہ 10 تک منتخب پروپوزلز کا جائزہ لیا جاتا ہے (3 ماہ میں 30 تک) — یہ جائزہ لیے گئے پروپوزلز کی حد ہے، رشتے کی ضمانت نہیں۔',
            'features' => [
                'Everything in Assisted Matchmaking',
                'Dedicated personal consultant',
                'Detailed requirement interview',
                'Priority database search',
                'Profile optimization',
                'Up to 30 curated proposals reviewed over 3 months (subject to availability & compatibility)',
                'Compatibility review',
                'Priority follow-ups',
                'Family coordination assistance',
                'Scheduled consultation calls',
                'Weekly progress updates',
                'Priority support',
            ],
            'features_ur' => [
                'معاون میچ میکنگ کی تمام سہولیات',
                'مخصوص ذاتی کنسلٹنٹ',
                'تفصیلی ضروریات کا انٹرویو',
                'ترجیحی ڈیٹا بیس تلاش',
                'پروفائل کی بہتری',
                '3 ماہ میں 30 تک منتخب پروپوزلز کا جائزہ (دستیابی اور مطابقت سے مشروط)',
                'مطابقت کا جائزہ',
                'ترجیحی پیروی',
                'خاندانی رابطہ کاری میں معاونت',
                'شیڈول شدہ مشاورتی کالز',
                'ہفتہ وار پیش رفت اپڈیٹس',
                'ترجیحی سپورٹ',
            ],
            'color' => 'blue',
            'icon' => '🔵',
            'sort_order' => 3,
            'is_active' => true,
            'show_on_public_page' => true,
        ]);

        // Inactive on purpose — enable + set show_on_public_page once there's
        // a database and success stories to justify the price (see the
        // package's own description).
        NikahPackage::updateOrCreate(['slug' => 'premium-vip'], [
            'name' => 'Sallaamti Premium / VIP',
            'name_ur' => 'سلامتی پریمیئم / وی آئی پی',
            'tagline' => 'Dedicated senior consultant, fully bespoke search',
            'tagline_ur' => 'مخصوص سینئر کنسلٹنٹ، مکمل طور پر آپ کے مطابق تلاش',
            'price' => 30000,
            'duration_days' => 90,
            'proposal_limit' => null,
            'consultant_level' => 'Dedicated senior consultant',
            'description' => 'A fully bespoke matchmaking service for highly specific requirements, overseas Pakistani searches, and confidential profiles — extensive manual research by a senior consultant rather than a fixed proposal count. Held back from public launch until the database and track record justify it.',
            'description_ur' => 'انتہائی مخصوص ضروریات، بیرونِ ملک پاکستانیوں کی تلاش، اور خفیہ پروفائلز کے لیے ایک مکمل طور پر حسبِ ضرورت میچ میکنگ سروس — ایک مقررہ پروپوزل تعداد کے بجائے سینئر کنسلٹنٹ کی جانب سے وسیع دستی تحقیق۔ ڈیٹا بیس اور کامیابی کی شرح اس کے قابل ہونے تک عوامی لانچ کے لیے روکا گیا ہے۔',
            'features' => [
                'Everything in Premium Personal Matchmaking',
                'Dedicated senior consultant',
                'Highly specific requirement handling',
                'Overseas Pakistani candidate search',
                'Confidential profile handling',
                'Family-to-family coordination',
                'Extensive manual research',
                'Priority access',
                'Personal consultation',
            ],
            'features_ur' => [
                'پریمیئم پرسنل میچ میکنگ کی تمام سہولیات',
                'مخصوص سینئر کنسلٹنٹ',
                'انتہائی مخصوص ضروریات کی ہینڈلنگ',
                'بیرونِ ملک پاکستانی امیدواروں کی تلاش',
                'خفیہ پروفائل ہینڈلنگ',
                'خاندان سے خاندان رابطہ کاری',
                'وسیع دستی تحقیق',
                'ترجیحی رسائی',
                'ذاتی مشاورت',
            ],
            'color' => 'red',
            'icon' => '🔴',
            'sort_order' => 4,
            'is_active' => false,
            'show_on_public_page' => false,
        ]);
    }
}
