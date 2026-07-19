<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FileStorage
{
    public const MAX_UPLOAD_KB = 10240;

    /**
     * Get the validation rules for service order attachments.
     */
    public static function attachmentValidationRules(bool $required = true): array
    {
        $attachmentRule = $required
            ? ['required', 'array', 'min:1']
            : ['nullable', 'array'];

        return [
            'attachments' => $attachmentRule,
            'attachments.*' => [
                'file',
                'mimes:' . config('upload.attachment_mimes', 'jpg,jpeg,png,webp,heic,heif,pdf,doc,docx,zip,rar,txt'),
                'max:' . config('upload.max_kb', self::MAX_UPLOAD_KB),
            ],
        ];
    }

    /**
     * Store a publicly accessible file (product images, logos, …).
     */
    public static function storePublic(UploadedFile $file, string $directory): string
    {
        self::ensureDirectory('public', $directory);

        $path = $file->store($directory, 'public');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('فایل ذخیره نشد. دسترسی نوشتن پوشه storage/app/public را بررسی کنید.');
        }

        return $path;
    }

    /**
     * Store a private file (service order attachments, …).
     */
    public static function storePrivate(UploadedFile $file, string $directory): string
    {
        self::ensureDirectory('local', $directory);

        $allowedMimes = explode(',', (string) config('upload.attachment_mimes', 'jpg,jpeg,png,webp,pdf,doc,docx,zip,rar,txt'));
        $allowedMimes = array_map('trim', $allowedMimes);
        $allowedMimes = array_filter($allowedMimes);

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getClientMimeType());

        if ($allowedMimes !== [] && ! in_array($extension, $allowedMimes, true) && ! in_array($mime, $allowedMimes, true)) {
            throw new RuntimeException('نوع فایل ضمیمه مجاز نیست.');
        }

        $filename = time().'_'.uniqid().'.'.$extension;
        $path = $file->storeAs($directory, $filename, 'local');

        if (! $path || ! Storage::disk('local')->exists($path)) {
            throw new RuntimeException('فایل ذخیره نشد. دسترسی نوشتن پوشه storage/app را بررسی کنید.');
        }

        return $path;
    }

    public static function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = self::normalizePath($path);

        if ($normalized === '' || ! self::publicExists($normalized)) {
            return null;
        }

        return '/storage/'.$normalized;
    }

    public static function publicExists(string $path): bool
    {
        $normalized = self::normalizePath($path);

        if ($normalized === '') {
            return false;
        }

        if (Storage::disk('public')->exists($normalized)) {
            return true;
        }

        $publicPath = public_path('storage/'.$normalized);

        return is_file($publicPath);
    }

    public static function deletePublic(string $path): void
    {
        Storage::disk('public')->delete(self::normalizePath($path));
    }

    public static function deletePrivate(string $path): void
    {
        Storage::disk('local')->delete(self::normalizePath($path));
    }

    /**
     * Ensure public/storage symlink exists and upload directories are present.
     *
     * @return array{link: bool, writable: bool, directories: list<string>}
     */
    public static function ensureDeploymentReady(): array
    {
        $directories = ['products', 'attachments', 'brand'];
        $created = [];

        foreach ($directories as $directory) {
            if (! Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                $created[] = "public:{$directory}";
            }
        }

        if (! Storage::disk('local')->exists('attachments')) {
            Storage::disk('local')->makeDirectory('attachments');
            $created[] = 'local:attachments';
        }

        $linkOk = self::ensurePublicLink();

        return [
            'link' => $linkOk,
            'writable' => is_writable(storage_path('app/public'))
                && is_writable(storage_path('app')),
            'directories' => $created,
        ];
    }

    public static function ensurePublicLink(): bool
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (is_link($link)) {
            return true;
        }

        if (is_dir($link) && ! is_link($link)) {
            return file_exists($link.'/.gitignore') || count(scandir($link)) > 2;
        }

        if (file_exists($link)) {
            return false;
        }

        try {
            return symlink($target, $link);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function normalizePath(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = parse_url($path, PHP_URL_PATH) ?? '';
        }

        $path = preg_replace('#^/storage/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        return ltrim(str_replace('\\', '/', $path), '/');
    }

    protected static function ensureDirectory(string $disk, string $directory): void
    {
        if (! Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }
    }
}
