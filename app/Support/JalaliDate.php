<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliDate
{
    public static function parse(?string $date): ?Carbon
    {
        if ($date === null || trim($date) === '') {
            return null;
        }

        $date = trim($date);
        $date = self::normalizeDigits($date);

        foreach (['Y/m/d', 'Y-m-d'] as $format) {
            try {
                return Jalalian::fromFormat($format, $date)->toCarbon();
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function startOfDay(?string $date): ?Carbon
    {
        return self::parse($date)?->copy()->startOfDay();
    }

    public static function endOfDay(?string $date): ?Carbon
    {
        return self::parse($date)?->copy()->endOfDay();
    }

    private static function normalizeDigits(string $value): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $ascii = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($persian, $ascii, str_replace($arabic, $ascii, $value));
    }
}
