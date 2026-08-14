<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = require database_path('seeders/data/translations_ur.php');

        foreach ($translations as $key => $value) {
            Translation::updateOrCreate(
                ['locale' => 'ur', 'group' => 'db', 'key' => $key],
                ['value' => $value]
            );
        }

        Translation::forgetCachedTranslations('ur');

        $this->command?->info(count($translations) . ' Urdu translations seeded.');
    }
}
