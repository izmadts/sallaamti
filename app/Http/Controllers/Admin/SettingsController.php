<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::allKeyed();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'             => ['required', 'string', 'max:100'],
            'site_tagline'          => ['nullable', 'string', 'max:255'],
            'site_email'            => ['nullable', 'email'],
            'site_phone'            => ['nullable', 'string', 'max:30'],
            'site_address'          => ['nullable', 'string', 'max:255'],
            'about_heading'    => ['nullable', 'string', 'max:255'],
            'about_text'       => ['nullable', 'string'],
            'vision_text'      => ['nullable', 'string'],
            'mission_text'     => ['nullable', 'string'],
            'donate_goal_text' => ['nullable', 'string', 'max:50'],

            //Payment settings
            'jazzcash_number'       => ['nullable', 'string', 'max:30'],
            'easypaisa_number'      => ['nullable', 'string', 'max:30'],
            'bank_account_title'    => ['nullable', 'string', 'max:100'],
            'bank_account_number'   => ['nullable', 'string', 'max:50'],
            'bank_name'             => ['nullable', 'string', 'max:100'],
            'nikah_verification_fee' => ['required', 'numeric', 'min:0'],
            'social_facebook'       => ['nullable', 'url'],
            'social_youtube'        => ['nullable', 'url'],
            'social_whatsapp'       => ['nullable', 'string', 'max:30'],
            'social_instagram'      => ['nullable', 'url'],
            'social_tiktok'         => ['nullable', 'url'],
            'maintenance_mode'      => ['nullable', 'boolean'],
        ]);

        // Save each setting
        $groups = [
            'site_name'              => 'general',
            'site_tagline'           => 'general',
            'site_email'             => 'general',
            'site_phone'             => 'general',
            'site_address'           => 'general',
            'maintenance_mode'       => 'general',
            'about_heading'          => 'about',
            'about_text'             => 'about',
            'vision_text'            => 'about',
            'mission_text'           => 'about',
            'donate_goal_text'       => 'about',
            'jazzcash_number'        => 'payment',
            'easypaisa_number'       => 'payment',
            'bank_account_title'     => 'payment',
            'bank_account_number'    => 'payment',
            'bank_name'              => 'payment',
            'nikah_verification_fee' => 'payment',
            'social_facebook'        => 'social',
            'social_youtube'         => 'social',
            'social_whatsapp'        => 'social',
            'social_instagram'       => 'social',
            'social_tiktok'          => 'social',
        ];

        foreach ($groups as $key => $group) {
            $value = $key === 'maintenance_mode'
                ? ($request->has('maintenance_mode') ? '1' : '0')
                : $request->input($key, '');

            Setting::set($key, $value, $group);
        }

        return back()->with('status', 'Settings saved successfully.');
    }
}
