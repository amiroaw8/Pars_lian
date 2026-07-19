<?php

declare(strict_types=1);

namespace App\Support;

final class BrandLogo
{
    public const RELATIVE_PATH = 'images/pars-lian-logo.png';

    public const HERO_RELATIVE_PATH = 'images/pars-lian-logo-hero.png';

    public static function path(): string
    {
        return public_path(self::RELATIVE_PATH);
    }

    public static function exists(): bool
    {
        return is_file(self::path());
    }

    public static function url(): string
    {
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

    public static function heroUrl(): string
    {
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

        $mime = mime_content_type(self::path()) ?: 'image/png';
        $data = base64_encode((string) file_get_contents(self::path()));

        return 'data:' . $mime . ';base64,' . $data;
    }
}
