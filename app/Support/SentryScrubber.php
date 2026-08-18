<?php

namespace App\Support;

use Sentry\Breadcrumb;
use Sentry\Event;

// Referenced from config/sentry.php as an array callable ([Class, 'method'])
// rather than an inline closure — Sentry's before_breadcrumb/
// before_send_transaction need to be *something* var_export() can
// re-serialize, since `php artisan config:cache` fails outright on a
// closure in a config file. A static-method callable is a plain string
// pair, so it survives caching.
//
// Why this exists at all: query strings on outbound HTTP client calls
// routinely carry live secrets in this app — OAuth token exchanges and
// Graph/TikTok API calls pass access_token/client_secret/refresh_token/code
// as GET params (see SocialIntegrationController and SocialPublishing/*).
// Sentry's HTTP client integration captures the full query string as
// 'http.query' on every breadcrumb and span regardless of send_default_pii,
// so without this redaction any error or sampled trace during a social
// OAuth flow or publish would ship live tokens to Sentry in plaintext.
class SentryScrubber
{
    private const SENSITIVE_QUERY_KEYS = ['access_token', 'client_secret', 'refresh_token', 'code_verifier', 'code', 'fb_exchange_token', 'token'];

    public static function scrubBreadcrumb(Breadcrumb $breadcrumb): Breadcrumb
    {
        $metadata = $breadcrumb->getMetadata();

        if (isset($metadata['http.query']) && is_string($metadata['http.query'])) {
            $breadcrumb = $breadcrumb->withMetadata('http.query', static::redactQueryString($metadata['http.query']));
        }

        if (isset($metadata['url']) && is_string($metadata['url'])) {
            $breadcrumb = $breadcrumb->withMetadata('url', static::redactUrl($metadata['url']));
        }

        return $breadcrumb;
    }

    // Breadcrumbs only cover error events — spans ship separately on
    // sampled performance transactions, so they need the same redaction.
    public static function scrubTransaction(Event $transaction): Event
    {
        foreach ($transaction->getSpans() as $span) {
            $query = $span->getData('http.query');
            if (is_string($query)) {
                $span->setData(['http.query' => static::redactQueryString($query)]);
            }

            $url = $span->getData('url');
            if (is_string($url)) {
                $span->setData(['url' => static::redactUrl($url)]);
            }
        }

        return $transaction;
    }

    private static function redactQueryString(string $query): string
    {
        parse_str($query, $params);
        $redacted = false;

        foreach (self::SENSITIVE_QUERY_KEYS as $key) {
            if (array_key_exists($key, $params)) {
                $params[$key] = '[Filtered]';
                $redacted = true;
            }
        }

        return $redacted ? http_build_query($params) : $query;
    }

    private static function redactUrl(string $url): string
    {
        $pattern = '/([?&])(' . implode('|', self::SENSITIVE_QUERY_KEYS) . ')=[^&]*/i';

        return preg_replace($pattern, '$1$2=[Filtered]', $url);
    }
}
