<?php

namespace Database\Seeders;

use App\Models\TrainingQuizQuestion;
use Illuminate\Database\Seeder;

// Trainee-access addendum (15/16 Nov 2026) — questions authored directly
// from the owner's own training document ("Sallaamti App mein ek client
// par kaam karne ka mukammal tareeqa"), one per real rule a new counselor
// needs to actually know, not trivia. Run once:
// php artisan db:seed --class=TrainingQuizQuestionSeeder
class TrainingQuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question_en' => 'A client contacts Counselor Fatima directly on WhatsApp and she creates the lead herself. Later, can admin move that lead to a different counselor?',
                'question_ur' => 'ایک کلائنٹ نے براہ راست کاؤنسلر فاطمہ سے واٹس ایپ پر رابطہ کیا اور فاطمہ نے خود اس کی لیڈ بنائی۔ کیا ایڈمن بعد میں یہ لیڈ کسی دوسرے کاؤنسلر کو دے سکتا ہے؟',
                'options' => [
                    ['text_en' => 'Yes, admin can reassign any lead at any time', 'text_ur' => 'ہاں، ایڈمن کسی بھی وقت کوئی بھی لیڈ منتقل کر سکتا ہے'],
                    ['text_en' => 'No — a lead the counselor created herself always stays hers', 'text_ur' => 'نہیں — جو لیڈ کاؤنسلر خود بناتی ہے وہ ہمیشہ اسی کی رہتی ہے'],
                    ['text_en' => 'Only if the client asks for a different counselor', 'text_ur' => 'صرف اگر کلائنٹ کسی دوسرے کاؤنسلر کی درخواست کرے'],
                    ['text_en' => 'Only after 5 days', 'text_ur' => 'صرف 5 دن کے بعد'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'Which counselors are eligible to receive new leads assigned by admin?',
                'question_ur' => 'ایڈمن کی طرف سے نئی لیڈز حاصل کرنے کے اہل کون سے کاؤنسلرز ہیں؟',
                'options' => [
                    ['text_en' => 'Only Certified counselors', 'text_ur' => 'صرف سرٹیفائیڈ کاؤنسلرز'],
                    ['text_en' => 'Only Probation and Certified counselors', 'text_ur' => 'صرف پروبیشن اور سرٹیفائیڈ کاؤنسلرز'],
                    ['text_en' => 'Active counselors at Training, Assessed, Probation, or Certified', 'text_ur' => 'ایکٹو کاؤنسلرز جو ٹریننگ، اسسمنٹ، پروبیشن یا سرٹیفائیڈ میں ہوں'],
                    ['text_en' => 'Anyone who has applied, regardless of stage', 'text_ur' => 'کوئی بھی جس نے درخواست دی ہو، اسٹیج سے قطع نظر'],
                ],
                'correct_index' => 2,
            ],
            [
                'question_en' => 'A lead\'s status hasn\'t changed in 5 days. What happens to it?',
                'question_ur' => 'ایک لیڈ کا اسٹیٹس 5 دن سے تبدیل نہیں ہوا۔ اس کے ساتھ کیا ہوگا؟',
                'options' => [
                    ['text_en' => 'Nothing — leads never expire', 'text_ur' => 'کچھ نہیں — لیڈز کبھی ایکسپائر نہیں ہوتیں'],
                    ['text_en' => 'It goes back to the unassigned pool (unless it\'s a lead the counselor created herself)', 'text_ur' => 'یہ واپس ان اسائنڈ پول میں چلی جاتی ہے (سوائے اس کے کہ کاؤنسلر نے خود یہ لیڈ بنائی ہو)'],
                    ['text_en' => 'It is automatically deleted', 'text_ur' => 'یہ خودکار طور پر ڈیلیٹ ہو جاتی ہے'],
                    ['text_en' => 'The client is automatically notified', 'text_ur' => 'کلائنٹ کو خودکار طور پر مطلع کیا جاتا ہے'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'A client submits their verification fee payment. When does the "Trusted" badge appear on their profile?',
                'question_ur' => 'ایک کلائنٹ اپنی ویریفکیشن فیس جمع کرواتا ہے۔ "Trusted" بیج ان کے پروفائل پر کب ظاہر ہوگا؟',
                'options' => [
                    ['text_en' => 'As soon as the payment is submitted', 'text_ur' => 'جیسے ہی ادائیگی جمع کروائی جائے'],
                    ['text_en' => 'Only after admin confirms the payment', 'text_ur' => 'صرف ایڈمن کی طرف سے ادائیگی کی تصدیق کے بعد'],
                    ['text_en' => 'As soon as the counselor submits it on the client\'s behalf', 'text_ur' => 'جیسے ہی کاؤنسلر کلائنٹ کی طرف سے جمع کروائے'],
                    ['text_en' => 'It never changes based on payment', 'text_ur' => 'یہ ادائیگی کی بنیاد پر کبھی تبدیل نہیں ہوتا'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'Is filling in a client\'s Requirements (preferred age, city, education, etc.) mandatory?',
                'question_ur' => 'کلائنٹ کی Requirements (پسندیدہ عمر، شہر، تعلیم وغیرہ) بھرنا لازمی ہے؟',
                'options' => [
                    ['text_en' => 'Yes, a batch can\'t be sent without it', 'text_ur' => 'ہاں، اس کے بغیر بیچ نہیں بھیجا جا سکتا'],
                    ['text_en' => 'No, it\'s optional — but it helps filter Browse results and improves the Match Score', 'text_ur' => 'نہیں، یہ اختیاری ہے — لیکن یہ براؤز نتائج فلٹر کرنے اور میچ سکور بہتر بنانے میں مدد دیتا ہے'],
                    ['text_en' => 'Only required for female clients', 'text_ur' => 'صرف خاتون کلائنٹس کے لیے ضروری ہے'],
                    ['text_en' => 'Only admin can fill this in', 'text_ur' => 'صرف ایڈمن یہ بھر سکتا ہے'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'What must exist before a Proposal Batch can be sent to a client?',
                'question_ur' => 'کلائنٹ کو Proposal Batch بھیجنے سے پہلے کیا چیز موجود ہونی چاہیے؟',
                'options' => [
                    ['text_en' => 'The client\'s recorded consent to participate in matchmaking', 'text_ur' => 'میچ میکنگ میں شرکت کے لیے کلائنٹ کی ریکارڈ شدہ رضامندی'],
                    ['text_en' => 'Nothing — any counselor can send a batch any time', 'text_ur' => 'کچھ نہیں — کوئی بھی کاؤنسلر کسی بھی وقت بیچ بھیج سکتا ہے'],
                    ['text_en' => 'The client must already be Certified', 'text_ur' => 'کلائنٹ کا پہلے سرٹیفائیڈ ہونا ضروری ہے'],
                    ['text_en' => 'Admin must create the batch himself', 'text_ur' => 'ایڈمن کو خود بیچ بنانا ضروری ہے'],
                ],
                'correct_index' => 0,
            ],
            [
                'question_en' => 'You are finding matches for a female client. Which candidates can you Browse?',
                'question_ur' => 'آپ ایک خاتون کلائنٹ کے لیے رشتے تلاش کر رہے ہیں۔ آپ کون سے امیدوار براؤز کر سکتے ہیں؟',
                'options' => [
                    ['text_en' => 'Only male candidates', 'text_ur' => 'صرف مرد امیدوار'],
                    ['text_en' => 'Only female candidates', 'text_ur' => 'صرف خاتون امیدوار'],
                    ['text_en' => 'Both genders — you choose per client', 'text_ur' => 'دونوں جنسیں — آپ ہر کلائنٹ کے مطابق چنیں'],
                    ['text_en' => 'Whichever gender admin assigns that day', 'text_ur' => 'جس دن جو جنس ایڈمن تفویض کرے'],
                ],
                'correct_index' => 0,
            ],
            [
                'question_en' => 'Adding a candidate to a client\'s Shortlist means...',
                'question_ur' => 'کسی امیدوار کو کلائنٹ کی Shortlist میں شامل کرنے کا مطلب ہے...',
                'options' => [
                    ['text_en' => 'The proposal has already been sent to the client', 'text_ur' => 'پروپوزل کلائنٹ کو پہلے ہی بھیجا جا چکا ہے'],
                    ['text_en' => 'It\'s only your own working list — nothing has been sent yet', 'text_ur' => 'یہ صرف آپ کی اپنی ورکنگ لسٹ ہے — ابھی کچھ نہیں بھیجا گیا'],
                    ['text_en' => 'The client has automatically accepted this candidate', 'text_ur' => 'کلائنٹ نے خودکار طور پر اس امیدوار کو قبول کر لیا ہے'],
                    ['text_en' => 'A Mutual Match has been created', 'text_ur' => 'میوچل میچ بن گیا ہے'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'What is the maximum number of candidates in one Proposal Batch?',
                'question_ur' => 'ایک Proposal Batch میں زیادہ سے زیادہ کتنے امیدوار شامل ہو سکتے ہیں؟',
                'options' => [
                    ['text_en' => '3', 'text_ur' => '3'],
                    ['text_en' => '5', 'text_ur' => '5'],
                    ['text_en' => '10', 'text_ur' => '10'],
                    ['text_en' => 'No limit', 'text_ur' => 'کوئی حد نہیں'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'A client marks a candidate as "Interested" on a proposal. What does this mean?',
                'question_ur' => 'ایک کلائنٹ کسی امیدوار کو پروپوزل پر "Interested" کہتا ہے۔ اس کا مطلب کیا ہے؟',
                'options' => [
                    ['text_en' => 'The rishta is finalized — nothing more needed', 'text_ur' => 'رشتہ طے ہو گیا — مزید کچھ درکار نہیں'],
                    ['text_en' => 'The system creates a normal interest, but the other side still has to accept it too before it\'s a Mutual Match', 'text_ur' => 'سسٹم ایک عام انٹرسٹ بناتا ہے، لیکن دوسری طرف سے بھی قبولیت ضروری ہے تاکہ میوچل میچ بنے'],
                    ['text_en' => 'The counselor must now call both families to arrange the Nikah', 'text_ur' => 'اب کاؤنسلر کو دونوں خاندانوں کو نکاح طے کرنے کے لیے کال کرنی ہوگی'],
                    ['text_en' => 'The candidate\'s contact details are shared immediately', 'text_ur' => 'امیدوار کی رابطہ تفصیلات فوراً شیئر کر دی جاتی ہیں'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'Can a Nikah Counselor force a Mutual Match on behalf of a candidate who hasn\'t responded?',
                'question_ur' => 'کیا نکاح کاؤنسلر کسی ایسے امیدوار کی طرف سے میوچل میچ بنا سکتا ہے جس نے ابھی جواب نہیں دیا؟',
                'options' => [
                    ['text_en' => 'Yes, if the counselor is confident they\'ll agree', 'text_ur' => 'ہاں، اگر کاؤنسلر کو یقین ہو کہ وہ راضی ہوں گے'],
                    ['text_en' => 'No — both sides\' real consent is required; the counselor only guides and follows up', 'text_ur' => 'نہیں — دونوں طرف کی اصل رضامندی ضروری ہے؛ کاؤنسلر صرف رہنمائی اور فالو اپ کرتا ہے'],
                    ['text_en' => 'Yes, but only for Certified counselors', 'text_ur' => 'ہاں، لیکن صرف سرٹیفائیڈ کاؤنسلرز کے لیے'],
                    ['text_en' => 'Only admin can force a match', 'text_ur' => 'صرف ایڈمن زبردستی میچ بنا سکتا ہے'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'Where is every meaningful action on a client (status change, consent, batch sent, etc.) recorded?',
                'question_ur' => 'کلائنٹ پر ہر اہم ایکشن (اسٹیٹس تبدیلی، رضامندی، بیچ بھیجنا وغیرہ) کہاں ریکارڈ ہوتا ہے؟',
                'options' => [
                    ['text_en' => 'Nowhere — it\'s not tracked', 'text_ur' => 'کہیں نہیں — یہ ٹریک نہیں ہوتا'],
                    ['text_en' => 'In the client\'s Activity Timeline, automatically', 'text_ur' => 'کلائنٹ کی ایکٹیویٹی ٹائم لائن میں، خودکار طور پر'],
                    ['text_en' => 'Only if the counselor remembers to write a note', 'text_ur' => 'صرف اگر کاؤنسلر نوٹ لکھنا یاد رکھے'],
                    ['text_en' => 'In a weekly email to admin only', 'text_ur' => 'صرف ایڈمن کو ہفتہ وار ای میل میں'],
                ],
                'correct_index' => 1,
            ],
            [
                'question_en' => 'A client\'s CNIC images and direct phone number — can a Nikah Counselor see these?',
                'question_ur' => 'کلائنٹ کی شناختی کارڈ کی تصاویر اور براہ راست فون نمبر — کیا نکاح کاؤنسلر یہ دیکھ سکتا ہے؟',
                'options' => [
                    ['text_en' => 'Yes, always, for every candidate', 'text_ur' => 'ہاں، ہمیشہ، ہر امیدوار کے لیے'],
                    ['text_en' => 'No — never shown; a candidate\'s contact is only shared after admin approves a Request Contact', 'text_ur' => 'نہیں — کبھی نہیں دکھائے جاتے؛ امیدوار کا رابطہ صرف ایڈمن کی منظوری کے بعد Request Contact سے شیئر ہوتا ہے'],
                    ['text_en' => 'Yes, but only for candidates from the same city', 'text_ur' => 'ہاں، لیکن صرف اسی شہر کے امیدواروں کے لیے'],
                    ['text_en' => 'Only after the client pays the verification fee', 'text_ur' => 'صرف کلائنٹ کی ویریفکیشن فیس ادائیگی کے بعد'],
                ],
                'correct_index' => 1,
            ],
        ];

        foreach ($questions as $i => $q) {
            TrainingQuizQuestion::updateOrCreate(
                ['question_en' => $q['question_en']],
                [
                    'question_ur' => $q['question_ur'],
                    'options' => $q['options'],
                    'correct_index' => $q['correct_index'],
                    'sort_order' => $i,
                ]
            );
        }
    }
}
