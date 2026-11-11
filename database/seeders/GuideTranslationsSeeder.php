<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

// One-off seeder for the Urdu strings added by the NC guide's matchmaking-
// workflow update (2692ddb) — the Translation table is this site's i18n
// store, so these rows exist locally but don't ship with a normal deploy.
// Run once on production after deploying: php artisan db:seed --class=GuideTranslationsSeeder
class GuideTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            "There's a Nikah Counselor mobile app" => 'ایک نکاح کاؤنسلر موبائل ایپ موجود ہے',
            "Once certified, you can manage your clients, shortlist candidates, and track proposals right from your phone — your admin will share the download with you. The website works fully too, and your own WhatsApp number remains just as valid for reaching prospects." => 'سرٹیفائیڈ ہونے کے بعد، آپ اپنے کلائنٹس کا انتظام، امیدواروں کی شارٹ لسٹ، اور تجاویز کی پیروی اپنے فون سے کر سکتے ہیں — ایڈمن آپ کو ڈاؤن لوڈ لنک فراہم کرے گا۔ ویب سائٹ بھی مکمل طور پر کام کرتی ہے، اور آپ کا اپنا واٹس ایپ نمبر بھی اتنا ہی کارآمد رہتا ہے۔',
            'Record their requirements against their client file — age range, city, sect, education, marital status, and more.' => 'ان کی ضروریات ان کی کلائنٹ فائل میں درج کریں — عمر کی حد، شہر، فرقہ، تعلیم، ازدواجی حیثیت، اور مزید۔',
            "Browse Sallaamti's verified Nikah profiles — automatically narrowed to what you recorded, so you spend less time filtering." => 'سلامتی کی تصدیق شدہ نکاح پروفائلز براؤز کریں — خودکار طور پر آپ کی درج کردہ ضروریات کے مطابق محدود ہو جاتی ہیں، تاکہ فلٹرنگ میں کم وقت لگے۔',
            "Add promising candidates, with the option to share any one's details straight to your WhatsApp chat with the client." => 'امید افزا امیدواروں کو شامل کریں، اور کسی کی بھی تفصیلات سیدھا کلائنٹ کے ساتھ اپنی واٹس ایپ چیٹ میں شیئر کرنے کا اختیار بھی۔',
            'Mark a candidate final' => 'امیدوار کو حتمی قرار دیں',
            'Once your client confirms they like someone, one action records their consent, moves that candidate into Proposals, and sets a 7-day follow-up automatically.' => 'جیسے ہی آپ کا کلائنٹ کسی کو پسند کرنے کی تصدیق کرے، ایک ہی عمل ان کی رضامندی ریکارڈ کر دیتا ہے، اس امیدوار کو تجاویز میں منتقل کر دیتا ہے، اور خودکار طور پر 7 دن کا فالو اپ مقرر کر دیتا ہے۔',
            'See the final match' => 'حتمی رشتہ دیکھیں',
            'Once your client responds "interested," it\'s highlighted clearly in Proposals — no digging through batches to find it.' => 'جیسے ہی آپ کا کلائنٹ "دلچسپی" میں جواب دے، یہ تجاویز میں واضح طور پر نمایاں ہو جاتا ہے — بیچز میں تلاش کرنے کی ضرورت نہیں۔',
            "Message them in-app if they're active on the Sallaamti member app, or keep using WhatsApp — either way, keep notes and follow up until — in sha Allah — they're matched." => 'اگر وہ سلامتی ممبر ایپ پر فعال ہیں تو ایپ کے اندر پیغام بھیجیں، ورنہ واٹس ایپ استعمال کرتے رہیں — دونوں صورتوں میں نوٹس رکھیں اور پیروی کرتے رہیں جب تک — ان شاء اللہ — ان کا رشتہ طے نہ ہو جائے۔',
        ];

        foreach ($rows as $key => $value) {
            Translation::updateOrCreate(['locale' => 'ur', 'group' => 'db', 'key' => $key], ['value' => $value]);
        }

        Translation::forgetCachedTranslations('ur');
    }
}
