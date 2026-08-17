<?php

namespace Database\Seeders;

use App\Models\DailyContent;
use Illuminate\Database\Seeder;

// A starter pool of widely-known, uncontroversial ayahs/hadiths so the daily
// widget has content from day one. Not a scholarly authority — the admin
// should review and expand this list via Admin > Daily Ayah / Hadith.
class DailyContentSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            ['type' => 'ayah', 'reference' => 'Quran 2:153', 'translation' => 'O you who believe! Seek help through patience and prayer. Indeed, Allah is with the patient.'],
            ['type' => 'ayah', 'reference' => 'Quran 2:186', 'translation' => 'And when My servants ask you concerning Me, indeed I am near. I respond to the invocation of the supplicant when he calls upon Me.'],
            ['type' => 'ayah', 'reference' => 'Quran 2:286', 'translation' => 'Allah does not burden a soul beyond that it can bear.'],
            ['type' => 'ayah', 'reference' => 'Quran 3:159', 'translation' => 'And by the mercy of Allah, you were lenient with them. And if you had been rude and harsh in heart, they would have disbanded from about you.'],
            ['type' => 'ayah', 'reference' => 'Quran 13:28', 'translation' => 'Verily, in the remembrance of Allah do hearts find rest.'],
            ['type' => 'ayah', 'reference' => 'Quran 16:97', 'translation' => 'Whoever does righteousness, whether male or female, while being a believer, We will surely cause him to live a good life.'],
            ['type' => 'ayah', 'reference' => 'Quran 20:25-26', 'translation' => 'My Lord, expand for me my chest and ease for me my task.'],
            ['type' => 'ayah', 'reference' => 'Quran 39:53', 'translation' => 'Say: O My servants who have transgressed against themselves, do not despair of the mercy of Allah. Indeed, Allah forgives all sins.'],
            ['type' => 'ayah', 'reference' => 'Quran 49:13', 'translation' => 'O mankind, indeed We have created you from male and female and made you peoples and tribes that you may know one another. Indeed, the most noble of you in the sight of Allah is the most righteous.'],
            ['type' => 'ayah', 'reference' => 'Quran 65:3', 'translation' => 'And whoever relies upon Allah, then He is sufficient for him. Indeed, Allah will accomplish His purpose.'],
            ['type' => 'ayah', 'reference' => 'Quran 94:5-6', 'translation' => 'For indeed, with hardship comes ease. Indeed, with hardship comes ease.'],
            ['type' => 'ayah', 'reference' => 'Quran 103:1-3', 'translation' => 'By time, indeed mankind is in loss, except for those who believe and do righteous deeds and advise each other to truth and patience.'],

            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 1', 'translation' => 'Actions are judged by intentions, and every person will be rewarded according to what they intended.'],
            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 13', 'translation' => 'None of you truly believes until he loves for his brother what he loves for himself.'],
            ['type' => 'hadith', 'reference' => 'Sahih Muslim 2586', 'translation' => 'Whoever does not show mercy to people, Allah will not show mercy to him.'],
            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 6018', 'translation' => 'Whoever believes in Allah and the Last Day should speak good or remain silent.'],
            ['type' => 'hadith', 'reference' => 'Sunan al-Tirmidhi 1987', 'translation' => 'Fear Allah wherever you are, and follow up a bad deed with a good one which will wipe it out, and behave well towards people.'],
            ['type' => 'hadith', 'reference' => 'Sahih Muslim 2664', 'translation' => 'The strong believer is better and more beloved to Allah than the weak believer, while there is good in both.'],
            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 5678', 'translation' => 'There is no fatigue, nor disease, nor sorrow, nor sadness, nor hurt, nor distress that befalls a believer, even if it were the prick of a thorn, except that Allah expiates some of his sins for that.'],
            ['type' => 'hadith', 'reference' => 'Sunan Ibn Majah 4132', 'translation' => 'Be in this world as if you were a stranger or a traveler.'],
            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 6114', 'translation' => 'The strong is not the one who overcomes people by his strength, but the strong is the one who controls himself while in anger.'],
            ['type' => 'hadith', 'reference' => 'Sahih al-Bukhari 6407', 'translation' => 'Whoever is deprived of gentleness is deprived of goodness.'],
        ];

        foreach ($entries as $entry) {
            DailyContent::updateOrCreate(
                ['reference' => $entry['reference']],
                ['type' => $entry['type'], 'translation' => $entry['translation'], 'is_active' => true]
            );
        }
    }
}
