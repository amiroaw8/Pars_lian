<?php

use App\Support\HashRef;
use Illuminate\Support\HtmlString;

if (! function_exists('hash_ref')) {
    function hash_ref(string|int|null $value, bool $withHash = true): HtmlString
    {
        return HashRef::html($value, $withHash);
    }
}

if (! function_exists('hash_ref_plain')) {
    function hash_ref_plain(string|int|null $value, bool $withHash = true): string
    {
        return HashRef::plain($value, $withHash);
    }
}

if (! function_exists('jalali_date')) {
    /**
     * Format a date/time for display in the Jalali calendar.
     */
    function jalali_date(mixed $date, string $format = 'Y/m/d H:i', ?string $timezone = 'Asia/Tehran'): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        try {
            $carbon = $date instanceof \DateTimeInterface
                ? \Illuminate\Support\Carbon::instance(
                    $date instanceof \DateTimeImmutable
                        ? \DateTime::createFromImmutable($date)
                        : $date
                )
                : \Illuminate\Support\Carbon::parse($date);

            if ($timezone !== null) {
                $carbon = $carbon->timezone($timezone);
            }

            return \Morilog\Jalali\Jalalian::fromDateTime($carbon)->format($format);
        } catch (\Throwable) {
            return null;
        }
    }
}
