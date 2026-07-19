<?php

namespace App\Support;

class ShopFormat
{
    /** Display amount stored as Toman (integer, no decimals). */
    public static function money(float|int|string|null $amount): string
    {
        return self::moneyAmount($amount) . ' تومان';
    }

    public static function moneyAmount(float|int|string|null $amount): string
    {
        return number_format(self::toIntegerAmount($amount));
    }

    public static function toIntegerAmount(float|int|string|null $amount): int
    {
        if ($amount === null || $amount === '') {
            return 0;
        }

        if (is_int($amount)) {
            return max(0, $amount);
        }

        if (is_float($amount)) {
            return max(0, (int) round($amount));
        }

        if (is_string($amount)) {
            $normalized = trim($amount);
            $normalized = str_replace([',', '٬', ' ', '،'], '', $normalized);
            $normalized = str_replace(
                ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'],
                ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                $normalized
            );

            if ($normalized === '' || ! is_numeric($normalized)) {
                return 0;
            }

            return max(0, (int) floor((float) $normalized));
        }

        return max(0, (int) round((float) $amount));
    }

    public static function amountInWords(float|int|string|null $amount): string
    {
        $n = self::toIntegerAmount($amount);

        if ($n === 0) {
            return 'صفر تومان';
        }

        return self::convertNumberToPersianWords($n) . ' تومان';
    }

    public static function uniqueSlug(string $name, string $modelClass, ?int $ignoreId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        if ($slug === '') {
            $slug = 'item-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
        }

        $base = $slug;
        $i = 1;
        while ($modelClass::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** @param  array<string, mixed>|null  $specs */
    public static function normalizeTechnicalSpecs(?array $specs): ?array
    {
        if (empty($specs) || empty($specs['keys']) || ! is_array($specs['keys'])) {
            return null;
        }

        $result = [];
        foreach ($specs['keys'] as $index => $key) {
            $key = trim((string) $key);
            $value = trim((string) ($specs['values'][$index] ?? ''));
            if ($key !== '') {
                $result[$key] = $value;
            }
        }

        return $result ?: null;
    }

    private static function convertNumberToPersianWords(int $number): string
    {
        if ($number === 0) {
            return 'صفر';
        }

        $parts = [];
        $scales = [
            1_000_000_000 => 'میلیارد',
            1_000_000 => 'میلیون',
            1_000 => 'هزار',
        ];

        foreach ($scales as $value => $label) {
            if ($number >= $value) {
                $count = intdiv($number, $value);
                $number %= $value;
                $parts[] = self::convertHundreds($count) . ' ' . $label;
            }
        }

        if ($number > 0) {
            $parts[] = self::convertHundreds($number);
        }

        return trim(preg_replace('/\s+/', ' ', implode(' و ', array_filter($parts))));
    }

    private static function convertHundreds(int $number): string
    {
        $ones = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
        $teens = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];
        $tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
        $hundreds = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];

        $words = [];

        if ($number >= 100) {
            $words[] = $hundreds[intdiv($number, 100)];
            $number %= 100;
        }

        if ($number >= 10 && $number <= 19) {
            $words[] = $teens[$number - 10];
            $number = 0;
        } elseif ($number >= 20) {
            $words[] = $tens[intdiv($number, 10)];
            $number %= 10;
        }

        if ($number > 0 && $number < 10) {
            $words[] = $ones[$number];
        }

        return trim(implode(' و ', array_filter($words)));
    }
}
