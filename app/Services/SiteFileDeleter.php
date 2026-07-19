<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class SiteFileDeleter
{
    public function deleteByKey(string $key): string
    {
        $key = trim($key);

        if ($key === '') {
            throw new InvalidArgumentException('شناسه فایل نامعتبر است.');
        }

        if (preg_match('/^attachment-(\d+)$/', $key, $matches)) {
            return $this->deleteAttachment((int) $matches[1]);
        }

        if (preg_match('/^product-(\d+)-(.+)$/', $key, $matches)) {
            return $this->deleteProductImage((int) $matches[1], $matches[2]);
        }

        if (preg_match('/^brand-(\d+)-(.+)$/', $key, $matches)) {
            return $this->deleteBrandLogo((int) $matches[1], $matches[2]);
        }

        if (preg_match('/^orphan-(local|public)-(.+)$/', $key, $matches)) {
            return $this->deleteOrphanFile($matches[1], $matches[2]);
        }

        throw new InvalidArgumentException('نوع فایل برای حذف شناخته نشد.');
    }

    protected function deleteAttachment(int $id): string
    {
        $attachment = Attachment::find($id);

        if (! $attachment) {
            if (Attachment::onlyTrashed()->find($id)) {
                throw new RuntimeException('این فایل قبلاً به سطل زباله منتقل شده است.');
            }

            throw new RuntimeException('رکورد پیوست یافت نشد.');
        }

        $displayName = $attachment->name;
        $attachment->delete();

        $this->logDeletion('attachment_soft', $displayName, [
            'attachment_id' => $id,
            'path' => FileStorage::normalizePath((string) $attachment->path),
        ]);

        return "فایل «{$displayName}» به سطل زباله منتقل شد.";
    }

    public function purgeAttachment(Attachment $attachment): void
    {
        $storedPath = FileStorage::normalizePath((string) $attachment->path);

        if ($storedPath !== '') {
            if (Storage::disk('local')->exists($storedPath)) {
                FileStorage::deletePrivate($storedPath);
            } elseif (Storage::disk('public')->exists($storedPath)) {
                FileStorage::deletePublic($storedPath);
            }
        }

        if (! $attachment->trashed()) {
            $attachment->delete();
        }

        $attachment->forceDelete();

        $this->logDeletion('attachment_purge', (string) $attachment->name, [
            'attachment_id' => $attachment->id,
            'path' => $storedPath,
        ]);
    }

    protected function deleteProductImage(int $productId, string $path): string
    {
        $normalized = FileStorage::normalizePath($path);
        $product = Product::withTrashed()->find($productId);

        if (! $product) {
            throw new RuntimeException('محصول مرتبط یافت نشد.');
        }

        $images = collect($product->images ?? [])
            ->map(fn ($image) => FileStorage::normalizePath((string) $image))
            ->filter(fn ($image) => $image !== '' && $image !== $normalized)
            ->values()
            ->all();

        $product->update(['images' => $images]);

        if ($normalized !== '' && Storage::disk('public')->exists($normalized)) {
            FileStorage::deletePublic($normalized);
        }

        $this->logDeletion('product_image', basename($normalized), [
            'product_id' => $productId,
            'path' => $normalized,
        ]);

        return 'تصویر محصول حذف شد.';
    }

    protected function deleteBrandLogo(int $brandId, string $path): string
    {
        $normalized = FileStorage::normalizePath($path);
        $brand = Brand::withTrashed()->find($brandId);

        if (! $brand) {
            throw new RuntimeException('برند مرتبط یافت نشد.');
        }

        if (FileStorage::normalizePath((string) $brand->logo) === $normalized) {
            $brand->update(['logo' => null]);
        }

        if ($normalized !== '' && Storage::disk('public')->exists($normalized)) {
            FileStorage::deletePublic($normalized);
        }

        $this->logDeletion('brand_logo', basename($normalized), [
            'brand_id' => $brandId,
            'path' => $normalized,
        ]);

        return 'لوگوی برند حذف شد.';
    }

    protected function deleteOrphanFile(string $disk, string $path): string
    {
        if (! in_array($disk, ['local', 'public'], true)) {
            throw new InvalidArgumentException('دیسک نامعتبر است.');
        }

        $normalized = FileStorage::normalizePath($path);
        $allowedPrefixes = match ($disk) {
            'local' => ['attachments/'],
            'public' => ['products/', 'brand/'],
            default => [],
        };

        $allowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed || ! Storage::disk($disk)->exists($normalized)) {
            throw new RuntimeException('فایل روی دیسک یافت نشد.');
        }

        if ($disk === 'local') {
            FileStorage::deletePrivate($normalized);
        } else {
            FileStorage::deletePublic($normalized);
        }

        $this->logDeletion('orphan', basename($normalized), [
            'disk' => $disk,
            'path' => $normalized,
        ]);

        return 'فایل از دیسک حذف شد.';
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function logDeletion(string $type, string $name, array $context = []): void
    {
        Log::info("حذف فایل از مدیریت فایل‌ها: {$name}", array_merge([
            'type' => $type,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
        ], $context));
    }
}
