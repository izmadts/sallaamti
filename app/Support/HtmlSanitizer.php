<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    /**
     * Sanitize rich-text editor output before it's stored, stripping
     * script tags, inline event handlers, and anything else that would
     * let an author (blogger/manager/admin) inject executable content
     * into a page every visitor renders.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        static $purifier = null;

        if ($purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('HTML.Allowed', implode(',', [
                'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'ul', 'ol', 'li',
                'a[href|title|target|rel]',
                'img[src|alt|width|height]',
                'span[style]', 'div',
                'table', 'thead', 'tbody', 'tr', 'th', 'td',
            ]));
            $config->set('CSS.AllowedProperties', ['text-align', 'color']);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
            $config->set('HTML.TargetBlank', true);
            $config->set('Cache.SerializerPath', storage_path('framework/cache/purifier'));

            if (!is_dir(storage_path('framework/cache/purifier'))) {
                mkdir(storage_path('framework/cache/purifier'), 0755, true);
            }

            $purifier = new HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }

    /**
     * Sanitize content that may or may not already be HTML.
     *
     * The web's authoring surfaces use Trix and post real HTML; the mobile
     * apps have a plain multiline text field instead. Wrapping plain input in
     * <br>s before purifying means one stored value renders correctly on both
     * — without it, a post written on a phone collapses onto a single line
     * when the same record is read on the web.
     */
    public static function cleanAuthoredText(?string $input): ?string
    {
        if ($input === null || $input === '') {
            return $input;
        }

        $isHtml = $input !== strip_tags($input);

        return static::clean($isHtml ? $input : nl2br(e($input), false));
    }

    // Same trust level as clean() above (admin-authored only — bulk email
    // composers are gated behind nikah.manage-style admin permissions, never
    // open to members), but a broader allowlist: a newsletter-style HTML
    // template needs background colors, padding, and a styled "button" link
    // to look like anything, none of which clean()'s span-only/color-and-
    // text-align-only rules would survive. Kept separate from clean() rather
    // than loosening it globally, since that one also guards lesson/blog
    // content rendered on the public site.
    public static function cleanEmailHtml(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        static $purifier = null;

        if ($purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('HTML.Allowed', implode(',', [
                'p[style]', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote[style]',
                'h1[style]', 'h2[style]', 'h3[style]', 'h4[style]', 'h5[style]', 'h6[style]',
                'ul[style]', 'ol[style]', 'li[style]',
                'a[href|title|target|rel|style]',
                'img[src|alt|width|height|style]',
                'span[style]', 'div[style]',
                'table[style|width|cellpadding|cellspacing]', 'thead', 'tbody',
                'tr[style]', 'th[style]', 'td[style]',
            ]));
            // border-radius/display aren't in this HTMLPurifier version's
            // built-in CSS property definitions, so listing them here alone
            // wouldn't work — registering them directly below instead.
            $config->set('CSS.AllowedProperties', [
                'text-align', 'color', 'background-color', 'background',
                'padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
                'margin', 'margin-top', 'margin-bottom', 'margin-left', 'margin-right',
                'border', 'border-color', 'border-width', 'border-style',
                'font-size', 'font-weight', 'font-family', 'line-height',
                'width', 'max-width', 'height', 'text-decoration',
            ]);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
            $config->set('HTML.TargetBlank', true);
            $config->set('Cache.SerializerPath', storage_path('framework/cache/purifier'));

            if (!is_dir(storage_path('framework/cache/purifier'))) {
                mkdir(storage_path('framework/cache/purifier'), 0755, true);
            }

            // Not in this HTMLPurifier version's built-in CSS property list
            // (border-radius wasn't standardized when it was written) — a
            // rounded "button" look is common enough in a template to
            // register these directly rather than drop the feature.
            $cssDefinition = $config->getCSSDefinition();
            $cssDefinition->info['border-radius'] = new \HTMLPurifier_AttrDef_CSS_Length('0');
            $cssDefinition->info['display'] = new \HTMLPurifier_AttrDef_Enum(['block', 'inline', 'inline-block', 'none']);

            $purifier = new HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }
}
