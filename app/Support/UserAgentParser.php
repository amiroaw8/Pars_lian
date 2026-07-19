<?php

namespace App\Support;

class UserAgentParser
{
    public static function browserLabel(?string $userAgent): string
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return 'نامشخص';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'symfony') || str_contains($ua, 'guzzlehttp') || str_contains($ua, 'curl/')) {
            return 'درخواست داخلی / سیستم';
        }

        if (str_contains($ua, 'edg/')) {
            return 'Microsoft Edge';
        }

        if (str_contains($ua, 'firefox/')) {
            return 'Mozilla Firefox';
        }

        if (str_contains($ua, 'chrome/') && ! str_contains($ua, 'edg/')) {
            return 'Google Chrome';
        }

        if (str_contains($ua, 'safari/') && ! str_contains($ua, 'chrome/')) {
            return 'Safari';
        }

        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }

        return 'مرورگر دیگر';
    }
}
