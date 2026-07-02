<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['key' => 'site_name',          'value' => 'Sallaamti',                     'group' => 'general'],
            ['key' => 'site_tagline',        'value' => 'Empowering Through Knowledge',  'group' => 'general'],
            ['key' => 'site_email',          'value' => 'info@sallaamti.com',            'group' => 'general'],
            ['key' => 'site_phone',          'value' => '+92 300 0000000',               'group' => 'general'],
            ['key' => 'site_address',        'value' => 'Karachi, Pakistan',             'group' => 'general'],
            ['key' => 'maintenance_mode',    'value' => '0',                             'group' => 'general'],

            // Payment
            ['key' => 'jazzcash_number',     'value' => '03XX-XXXXXXX',                 'group' => 'payment'],
            ['key' => 'easypaisa_number',    'value' => '03XX-XXXXXXX',                 'group' => 'payment'],
            ['key' => 'bank_account_title',  'value' => 'Sallaamti',                    'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '',                             'group' => 'payment'],
            ['key' => 'bank_name',           'value' => '',                             'group' => 'payment'],

            // Nikah fee
            ['key' => 'nikah_verification_fee', 'value' => '500',                       'group' => 'payment'],

            // Social
            ['key' => 'social_facebook',    'value' => 'https://facebook.com/sallaamti', 'group' => 'social'],
            ['key' => 'social_youtube',     'value' => '',                               'group' => 'social'],
            ['key' => 'social_instagram',   'value' => '',                               'group' => 'social'],
            ['key' => 'social_tiktok',      'value' => '',                               'group' => 'social'],
            ['key' => 'social_whatsapp',    'value' => '',                               'group' => 'social'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
