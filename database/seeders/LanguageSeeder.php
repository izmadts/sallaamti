<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::updateOrCreate(
            ['language' => 'en'],
            ['name' => 'English', 'is_rtl' => false, 'is_active' => true, 'is_default' => true]
        );

        Language::updateOrCreate(
            ['language' => 'ur'],
            ['name' => 'اردو', 'is_rtl' => true, 'is_active' => true, 'is_default' => false]
        );

        Language::forgetCachedLanguage();
    }
}
