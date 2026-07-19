<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class HashRef
{
    public static function html(string|int|null $value, bool $withHash = true): HtmlString
    {
        $text = (string) $value;
        $display = $withHash ? '#'.$text : $text;

        return new HtmlString(
            '<bdi dir="ltr" class="hash-ref" translate="no">'.e($display).'</bdi>'
        );
    }

    public static function plain(string|int|null $value, bool $withHash = true): string
    {
        $text = (string) $value;

        return "\u{200E}".($withHash ? '#' : '').$text;
    }

    public static function inlineInText(string $text): HtmlString
    {
        if ($text === '') {
            return new HtmlString('—');
        }

        $html = preg_replace(
            '/#([\w-]+)/u',
            '<bdi dir="ltr" class="hash-ref" translate="no">#$1</bdi>',
            e($text)
        );

        return new HtmlString($html ?? e($text));
    }
}
