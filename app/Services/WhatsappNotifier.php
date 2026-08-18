<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;

// WhatsApp Business Platform (Cloud API) — deliberately separate from the
// SocialPublishing/* classes, which all publish a CommunityPost to a public
// feed. WhatsApp has no such surface (see SocialIntegrationController's
// connectWhatsapp() docblock); this only ever sends a business-initiated
// template message to an individual opted-in phone number, which is a
// fundamentally different capability and requires a template pre-approved
// in Meta Business Manager — free-form text cannot be sent outside a
// customer-initiated 24-hour session window.
class WhatsappNotifier
{
    private const API_VERSION = 'v19.0';

    /**
     * @param array<int, string> $bodyParams Positional {{1}}, {{2}}, ... values for the template's body component.
     * @return array{success: bool, error: ?string}
     */
    public function sendTemplate(SocialAccount $account, string $toPhone, string $templateName, array $bodyParams = []): array
    {
        $phoneNumberId = $account->external_account_id;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->normalizePhone($toPhone),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en_US'],
            ],
        ];

        if (!empty($bodyParams)) {
            $payload['template']['components'] = [[
                'type' => 'body',
                'parameters' => array_map(fn ($value) => ['type' => 'text', 'text' => (string) $value], $bodyParams),
            ]];
        }

        $response = Http::withToken($account->access_token)
            ->post("https://graph.facebook.com/" . self::API_VERSION . "/{$phoneNumberId}/messages", $payload);

        if ($response->failed()) {
            return ['success' => false, 'error' => $response->json('error.message') ?? ('HTTP ' . $response->status())];
        }

        return ['success' => true, 'error' => null];
    }

    // WhatsApp's Cloud API expects E.164 digits with no leading '+' or
    // formatting. This only strips non-digit characters (spaces, dashes,
    // a leading '+') — it does NOT add a missing country code. Members can
    // enter a purely local number (e.g. "03XXXXXXXXX", see ValidPhoneNumber),
    // which needs a country code prepended before it's a real WhatsApp
    // recipient. Deliberately left as a known gap while this feature ships
    // disabled — worth checking real opted-in numbers before flipping
    // whatsapp_notifications_enabled on, and adding country-code handling
    // here (or at signup) if local-format numbers turn out to be common.
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? $phone;
    }
}
