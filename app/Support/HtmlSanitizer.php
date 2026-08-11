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
}
