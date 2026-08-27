<?php

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('normalize_whatsapp_number')) {
    // wa.me links need a bare international-format digit string — no "+",
    // spaces, or dashes. Every admin-facing WhatsApp field's placeholder
    // shows "+92 3XX XXXXXXX" as an example of what a real number looks
    // like, so admins reasonably type it that way; without this, the
    // resulting https://wa.me/+92%20314... link silently fails to open a
    // chat. A bare local number starting with 0 (e.g. 0314...) is also
    // accepted and rewritten to the 92 country code, since that's the
    // other format people naturally type a Pakistani mobile number in.
    function normalize_whatsapp_number(?string $number): string
    {
        $digits = preg_replace('/\D/', '', $number ?? '');

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        }

        return $digits;
    }
}

if (!function_exists('whatsapp_link')) {
    // The Mobile Phone number IS the WhatsApp number for this business —
    // "WhatsApp Number" in settings is only an optional override for the
    // rare case a WhatsApp Business line differs from the general mobile.
    // Landline never applies here; it's call-only.
    function whatsapp_link(?string $message = null, ?string $number = null): string
    {
        $digits = normalize_whatsapp_number($number ?? (setting('social_whatsapp') ?: setting('site_phone')));

        $url = 'https://wa.me/' . $digits;

        return $message ? $url . '?text=' . urlencode($message) : $url;
    }
}
