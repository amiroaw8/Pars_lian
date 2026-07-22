<?php

declare(strict_types=1);

namespace App\Support;

final class BrandLogo
{
    public const RELATIVE_PATH = 'images/pars-lian-logo.webp';

    public const RELATIVE_PATH_WEBP = 'images/pars-lian-logo.webp';

    public const HERO_RELATIVE_PATH = 'images/pars-lian-logo-hero.png';

    public const HERO_RELATIVE_PATH_WEBP = 'images/pars-lian-logo-hero.webp';

    public static function path(): string
    {
        return public_path(self::RELATIVE_PATH);
    }

    public static function exists(): bool
    {
        return is_file(self::path());
    }

    public static function webpPath(): string
    {
        return public_path(self::RELATIVE_PATH_WEBP);
    }

    public static function webpExists(): bool
    {
        return is_file(self::webpPath());
    }

    public static function webpUrl(): string
    {
        if (! self::webpExists()) {
            return '';
        }

        return asset(self::RELATIVE_PATH_WEBP) . '?v=' . filemtime(self::webpPath());
    }

    public static function url(): string
    {
        if (self::webpExists()) {
            return self::webpUrl();
        }

        if (! self::exists()) {
            return '';
        }

        return asset(self::RELATIVE_PATH) . '?v=' . filemtime(self::path());
    }

    public static function heroPath(): string
    {
        return public_path(self::HERO_RELATIVE_PATH);
    }

    public static function heroExists(): bool
    {
        return is_file(self::heroPath());
    }

    public static function heroWebpPath(): string
    {
        return public_path(self::HERO_RELATIVE_PATH_WEBP);
    }

    public static function heroWebpExists(): bool
    {
        return is_file(self::heroWebpPath());
    }

    public static function heroWebpUrl(): string
    {
        if (! self::heroWebpExists()) {
            return '';
        }

        return asset(self::HERO_RELATIVE_PATH_WEBP) . '?v=' . filemtime(self::heroWebpPath());
    }

    public static function heroUrl(): string
    {
        if (self::heroWebpExists()) {
            return self::heroWebpUrl();
        }

        if (! self::heroExists()) {
            return self::url();
        }

        return asset(self::HERO_RELATIVE_PATH) . '?v=' . filemtime(self::heroPath());
    }

    public static function dataUri(): string
    {
        if (! self::exists()) {
            return '';
        }

        $path = self::path();
        $mime = mime_content_type($path) ?: self::mimeTypeForPath($path);
        $data = base64_encode((string) file_get_contents($path));

        return 'data:' . $mime . ';base64,' . $data;
    }

    private static function mimeTypeForPath(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
}
