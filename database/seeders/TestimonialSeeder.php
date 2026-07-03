<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Muhammad Ahmed', 'location' => 'Lahore', 'rating' => 5, 'content' => "I had been able to read the Qur'an since childhood, but I never truly understood its message. Through Sallaamti's Quran Learning & Understanding Program, I learned Tajweed and the meanings of many verses. It has strengthened my connection with Allah and brought peace into my daily life.", 'order' => 1],
            ['name' => 'Ayesha Javaid', 'location' => 'UK', 'rating' => 5, 'content' => "The Sallaamti Nikah team guided us with sincerity, professionalism, and Islamic values throughout the process. They took the time to understand our family's expectations and helped us find a compatible match. Alhamdulillah, we are happily married.", 'order' => 2],
            ['name' => 'Abdullah & Maryam', 'location' => 'Rawalpindi', 'rating' => 5, 'content' => "After marriage, we faced communication challenges that affected our relationship. The counselors at Sallaamti listened without judgment and provided practical guidance based on Islamic principles.", 'order' => 3],
            ['name' => 'Ahmed Azeem', 'location' => 'Canada', 'rating' => 5, 'content' => "The teachers are knowledgeable, patient, and genuinely care about every student. The online classes are well organized, making it easy to learn even with a busy schedule. My children now enjoy reading the Qur'an.", 'order' => 4],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}
