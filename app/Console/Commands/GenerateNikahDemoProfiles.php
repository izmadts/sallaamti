<?php

namespace App\Console\Commands;

use App\Models\NikahProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateNikahDemoProfiles extends Command
{
    protected $signature = 'nikah:demo-profiles {count=10 : How many demo profiles to create}';

    protected $description = 'Seed placeholder verified Nikah profiles (clearly tagged is_demo) so the homepage counters aren\'t stuck at 0 during cold start. Remove later with nikah:demo-profiles-remove.';

    private array $maleNames = ['Ahmed Raza', 'Bilal Hussain', 'Usman Tariq', 'Hamza Sheikh', 'Faisal Iqbal', 'Zeeshan Malik', 'Adeel Farooq', 'Kashif Mahmood', 'Imran Aziz', 'Waqas Ahmed'];
    private array $femaleNames = ['Ayesha Siddiqui', 'Sana Riaz', 'Mahnoor Khan', 'Zainab Butt', 'Hira Yousaf', 'Sadia Anjum', 'Rabia Naz', 'Amna Shahid', 'Komal Ashraf', 'Iqra Bashir'];
    private array $cities = ['Lahore', 'Karachi', 'Islamabad', 'Faisalabad', 'Multan', 'Rawalpindi', 'Peshawar', 'Sialkot'];
    private array $sects = ['Sunni', 'Shia', 'Ahle Hadith', 'Deobandi'];
    private array $educations = ['BSc Computer Science', 'MBA', 'MBBS', 'BA English', 'MSc Chemistry', 'Bachelor of Engineering'];
    private array $professions = ['Software Engineer', 'Doctor', 'Teacher', 'Accountant', 'Business Owner', 'Bank Officer'];

    public function handle(): int
    {
        $count = max(1, (int) $this->argument('count'));

        for ($i = 0; $i < $count; $i++) {
            $gender = $i % 2 === 0 ? 'male' : 'female';
            $name = $gender === 'male'
                ? $this->maleNames[array_rand($this->maleNames)]
                : $this->femaleNames[array_rand($this->femaleNames)];

            $user = User::create([
                'name' => $name,
                'email' => 'demo.' . Str::slug($name) . '.' . Str::random(4) . '@sallaamti.demo',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'gender' => $gender,
            ]);

            NikahProfile::create([
                'user_id' => $user->id,
                'public_token' => Str::random(32),
                'age' => rand(22, 35),
                'height' => rand(5, 6) . "'" . rand(0, 11) . '"',
                'marital_status' => 'never_married',
                'sect' => $this->sects[array_rand($this->sects)],
                'education' => $this->educations[array_rand($this->educations)],
                'profession' => $this->professions[array_rand($this->professions)],
                'city' => $this->cities[array_rand($this->cities)],
                'country' => 'Pakistan',
                'family_type' => rand(0, 1) ? 'Nuclear' : 'Joint',
                'guardian_name' => 'Father of ' . $name,
                'guardian_contact' => '0300' . str_pad((string) rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'guardian_relation' => 'Father',
                'about' => 'Assalamu Alaikum, looking for a righteous life partner who values deen and family.',
                // Demo CNIC — clearly fake pattern, unique per row (microtime + index avoids
                // any collision with the real cnic_number unique constraint).
                'cnic_number' => '00000-' . substr(str_replace('.', '', (string) microtime(true)), -7) . str_pad((string) $i, 3, '0', STR_PAD_LEFT) . '-0',
                'allow_photo_sharing' => false,
                'verification_status' => 'verified',
                'is_demo' => true,
                'visibility' => 'public',
                'is_active' => true,
                'payment_status' => 'confirmed',
                'payment_amount' => 0,
                'payment_confirmed_at' => now(),
            ]);
        }

        $this->info("Created {$count} demo Nikah profiles (tagged is_demo). Remove them anytime with: php artisan nikah:demo-profiles-remove");

        return self::SUCCESS;
    }
}
