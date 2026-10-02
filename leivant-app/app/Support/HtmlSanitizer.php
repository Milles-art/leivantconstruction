<?php

namespace App\Support;

/**
 * Minimal HTML sanitizer for inbound email bodies and signatures shown in
 * the admin mail inbox. Strips executable/embedding elements, event-handler
 * attributes, and javascript:/data: URLs while keeping formatting tags.
 */
class HtmlSanitizer
{
    /** @var string[] */
    private const STRIPPED_ELEMENTS = [
        'script', 'iframe', 'object', 'embed', 'applet',
        'form', 'input', 'button', 'select', 'textarea',
        'link', 'meta', 'base', 'title', 'frame', 'frameset',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?>'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        foreach (self::STRIPPED_ELEMENTS as $tag) {
            $nodes = $document->getElementsByTagName($tag);
            for ($i = $nodes->length - 1; $i >= 0; $i--) {
                $node = $nodes->item($i);
                if ($node && $node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        $xpath = new \DOMXPath($document);

        // Strip event-handler attributes (onclick, onerror, ...).
        foreach ($xpath->query('//@*[starts-with(name(), "on")]') as $attribute) {
            if ($attribute->ownerElement) {
                $attribute->ownerElement->removeAttribute($attribute->nodeName);
            }
        }

        // Strip javascript:/data:/vbscript: URLs from linkable attributes.
        foreach ($xpath->query('//@*[name()="href" or name()="src" or name()="action" or name()="xlink:href" or name()="poster"]') as $attribute) {
            if ($attribute->ownerElement && preg_match('~^\s*(javascript|data|vbscript)\s*:~i', (string) $attribute->value)) {
                $attribute->ownerElement->removeAttribute($attribute->nodeName);
            }
        }

        $cleaned = $document->saveHTML() ?: '';

        // Remove the encoding processing instruction used for UTF-8 parsing.
        $cleaned = preg_replace('/<\?xml[^?]*\?>\s*/', '', $cleaned);

        return $cleaned ?? '';
    }
}
