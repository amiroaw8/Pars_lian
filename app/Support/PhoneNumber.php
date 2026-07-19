<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class PhoneNumber
{
    public static function digits(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    public static function tail10(?string $phone): string
    {
        $digits = self::digits($phone);

        return strlen($digits) >= 10 ? substr($digits, -10) : $digits;
    }

    public static function scopeWherePhoneMatches(Builder $query, string $column, ?string $phone): Builder
    {
        $tail = self::tail10($phone);
        if ($tail === '') {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereRaw(
            "REPLACE(REPLACE(REPLACE({$column}, '-', ''), ' ', ''), '+', '') LIKE ?",
            ["%{$tail}%"]
        );
    }
}
