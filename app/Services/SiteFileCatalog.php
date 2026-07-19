<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\ActivityLog;
use App\Support\HashRef;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SiteFileCatalog
{
    /** @var array<string, string> */
    public const CATEGORY_LABELS = [
        'App\Models\ServiceOrder' => 'پیوست سفارش تعمیر',
        'App\Models\Customer' => 'فایل مشتری',
        'App\Models\Product' => 'تصویر/فایل محصول',
        'App\Models\User' => 'فایل کاربر',
        'product_image' => 'تصویر محصول (فروشگاه)',
        'brand_logo' => 'لوگوی برند',
        'disk_orphan' => 'فایل بدون رکورد دیتابیس (باقی‌مانده روی دیسک)',
    ];

    /**
     * @return Collection<int, object{
     *     key: string,
     *     name: string,
     *     category: string,
     *     related_label: ?string,
     *     related_url: ?string,
     *     size: int,
     *     human_size: string,
     *     uploader_name: ?string,
     *     created_at: ?Carbon,
     *     download_url: string,
     * }>
     */
    public function all(): Collection
    {
        $entries = collect()
            ->merge($this->attachmentEntries())
            ->merge($this->productImageEntries())
            ->merge($this->brandLogoEntries())
            ->merge($this->diskOrphanEntries());

        return $entries
            ->unique('key')
            ->sortByDesc(fn ($file) => $file->created_at?->timestamp ?? 0)
            ->values();
    }

    public function grouped(): Collection
    {
        return $this->all()->groupBy(fn ($file) => $file->category);
    }

    public function stats(): array
    {
        $all = $this->all();

        return [
            'total' => $all->count(),
            'total_size' => $all->sum('size'),
            'by_category' => $all->groupBy('category')->map->count(),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    protected function attachmentEntries(): Collection
    {
        return Attachment::query()
            ->with(['uploader:id,name'])
            ->latest()
            ->get()
            ->map(function (Attachment $file) {
                $parent = $this->resolveAttachable($file);
                [$relatedLabel, $relatedUrl] = $this->relatedForAttachable(
                    $file->attachable_type,
                    $file->attachable_id,
                    $parent
                );

                return (object) [
                    'key' => 'attachment-'.$file->id,
                    'name' => $file->name,
                    'display_name' => $file->name,
                    'category' => self::CATEGORY_LABELS[$file->attachable_type ?? ''] ?? 'سایر فایل‌ها',
                    'related_label' => $relatedLabel,
                    'related_url' => $relatedUrl,
                    'mime_label' => $this->mimeLabelFromType((string) $file->mime_type),
                    'storage_label' => str_starts_with(FileStorage::normalizePath((string) $file->path), 'attachments/')
                        ? 'خصوصی · پیوست تعمیر'
                        : 'عمومی',
                    'storage_path' => FileStorage::normalizePath((string) $file->path),
                    'status_note' => null,
                    'size' => (int) ($file->size ?? 0),
                    'human_size' => $file->human_readable_size,
                    'uploader_name' => $file->uploader?->name,
                    'created_at' => $file->created_at,
                    'download_url' => route('automation.attachments.download', ['attachment' => $file->id]),
                    'is_image' => str_starts_with((string) $file->mime_type, 'image/'),
                    'can_delete' => true,
                    'delete_key' => 'attachment-'.$file->id,
                ];
            });
    }

    /**
     * @return Collection<int, object>
     */
    protected function productImageEntries(): Collection
    {
        $entries = collect();

        Product::withTrashed()
            ->whereNotNull('images')
            ->get(['id', 'name', 'images', 'created_at', 'deleted_at'])
            ->each(function (Product $product) use ($entries) {
                foreach ($product->images ?? [] as $path) {
                    $normalized = FileStorage::normalizePath((string) $path);
                    if ($normalized === '') {
                        continue;
                    }

                    $entries->push((object) [
                        'key' => 'product-'.$product->id.'-'.$normalized,
                        'name' => basename($normalized),
                        'display_name' => basename($normalized),
                        'category' => self::CATEGORY_LABELS['product_image'],
                        'related_label' => $product->trashed()
                            ? 'محصول: '.$product->name.' (حذف‌شده)'
                            : 'محصول: '.$product->name,
                        'related_url' => $product->trashed()
                            ? null
                            : $this->safeRoute('admin.products.edit', ['product' => $product->id]),
                        'mime_label' => $this->mimeLabelForPath($normalized, 'public'),
                        'storage_label' => 'عمومی · تصویر محصول',
                        'storage_path' => 'storage/app/public/'.$normalized,
                        'status_note' => $product->trashed() ? 'محصول مرتبط حذف شده' : null,
                        'size' => $this->publicFileSize($normalized),
                        'human_size' => $this->formatBytes($this->publicFileSize($normalized)),
                        'uploader_name' => null,
                        'created_at' => $product->created_at,
                        'download_url' => FileStorage::publicUrl($normalized) ?? '#',
                        'is_image' => $this->isImagePath($normalized),
                        'can_delete' => true,
                        'delete_key' => 'product-'.$product->id.'-'.$normalized,
                    ]);
                }
            });

        return $entries;
    }

    /**
     * @return Collection<int, object>
     */
    protected function brandLogoEntries(): Collection
    {
        return Brand::withTrashed()
            ->whereNotNull('logo')
            ->where('logo', '!=', '')
            ->get(['id', 'name', 'logo', 'created_at', 'deleted_at'])
            ->map(function (Brand $brand) {
                $normalized = FileStorage::normalizePath((string) $brand->logo);

                return (object) [
                    'key' => 'brand-'.$brand->id.'-'.$normalized,
                    'name' => basename($normalized),
                    'display_name' => basename($normalized),
                    'category' => self::CATEGORY_LABELS['brand_logo'],
                    'related_label' => $brand->trashed()
                        ? 'برند: '.$brand->name.' (حذف‌شده)'
                        : 'برند: '.$brand->name,
                    'related_url' => null,
                    'mime_label' => $this->mimeLabelForPath($normalized, 'public'),
                    'storage_label' => 'عمومی · لوگوی برند',
                    'storage_path' => 'storage/app/public/'.$normalized,
                    'status_note' => $brand->trashed() ? 'برند مرتبط حذف شده' : null,
                    'size' => $this->publicFileSize($normalized),
                    'human_size' => $this->formatBytes($this->publicFileSize($normalized)),
                    'uploader_name' => null,
                    'created_at' => $brand->created_at,
                    'download_url' => FileStorage::publicUrl($normalized) ?? '#',
                    'is_image' => $this->isImagePath($normalized),
                    'can_delete' => true,
                    'delete_key' => 'brand-'.$brand->id.'-'.$normalized,
                ];
            });
    }

    /**
     * Public files on disk not referenced in DB.
     *
     * @return Collection<int, object>
     */
    protected function diskOrphanEntries(): Collection
    {
        $referenced = $this->referencedPublicPaths();
        $deletedHints = $this->deletedAttachmentHintsByBasename();
        $entries = collect();

        foreach (['products', 'brand'] as $directory) {
            if (! Storage::disk('public')->exists($directory)) {
                continue;
            }

            foreach (Storage::disk('public')->allFiles($directory) as $path) {
                $normalized = FileStorage::normalizePath($path);
                if (isset($referenced[$normalized])) {
                    continue;
                }

                $basename = basename($normalized);
                $entries->push($this->buildOrphanEntry(
                    disk: 'public',
                    normalized: $normalized,
                    basename: $basename,
                    directory: $directory,
                    deletedHints: $deletedHints,
                ));
            }
        }

        $attachmentPaths = Attachment::withTrashed()->pluck('path')->map(
            fn ($path) => FileStorage::normalizePath((string) $path)
        )->flip();

        if (Storage::disk('local')->exists('attachments')) {
            foreach (Storage::disk('local')->allFiles('attachments') as $path) {
                $normalized = FileStorage::normalizePath($path);
                if ($attachmentPaths->has($normalized)) {
                    continue;
                }

                $basename = basename($normalized);
                $entries->push($this->buildOrphanEntry(
                    disk: 'local',
                    normalized: $normalized,
                    basename: $basename,
                    directory: 'attachments',
                    deletedHints: $deletedHints,
                ));
            }
        }

        return $entries;
    }

    /**
     * @param  Collection<string, object>  $deletedHints
     */
    protected function buildOrphanEntry(
        string $disk,
        string $normalized,
        string $basename,
        string $directory,
        Collection $deletedHints,
    ): object {
        $hint = $deletedHints->get($basename);
        $oldValues = is_object($hint) ? (array) ($hint->old_values ?? []) : [];

        $displayName = (string) ($oldValues['name'] ?? $basename);
        $mimeLabel = $this->mimeLabelForPath($normalized, $disk);
        $isImage = $this->isImagePath($normalized) || str_starts_with($mimeLabel, 'تصویر');

        $size = $disk === 'public'
            ? $this->publicFileSize($normalized)
            : (int) (Storage::disk('local')->size($normalized) ?: 0);

        $createdAt = $this->parseStorageFilenameTimestamp($basename)
            ?? ($disk === 'public' ? $this->publicFileMtime($normalized) : $this->privateFileMtime($normalized));

        [$relatedLabel, $relatedUrl, $uploaderName] = $this->orphanRelatedFromHint($oldValues, $hint);

        $storageLabel = match (true) {
            $directory === 'attachments' => 'خصوصی · پوشه پیوست تعمیر',
            $directory === 'products' => 'عمومی · پوشه تصاویر محصول',
            $directory === 'brand' => 'عمومی · پوشه لوگوی برند',
            default => $disk === 'local' ? 'خصوصی' : 'عمومی',
        };

        $statusNote = match (true) {
            $hint && ($hint->event ?? '') === 'deleted' => 'رکورد پیوست از دیتابیس حذف شده؛ فایل فیزیکی هنوز روی دیسک است',
            $hint && ($hint->event ?? '') === 'created' => 'رکورد دیتابیس یافت نشد؛ اطلاعات از لاگ ثبت اولیه بازیابی شده',
            $directory === 'attachments' => 'احتمالاً پیوست سفارش تعمیر بدون رکورد دیتابیس (آپلود ناقص یا حذف سخت رکورد)',
            default => 'فایل روی دیسک بدون ارجاع در دیتابیس',
        };

        return (object) [
            'key' => 'orphan-'.$disk.'-'.$normalized,
            'name' => $basename,
            'display_name' => $displayName,
            'category' => self::CATEGORY_LABELS['disk_orphan'],
            'related_label' => $relatedLabel,
            'related_url' => $relatedUrl,
            'mime_label' => $mimeLabel,
            'storage_label' => $storageLabel,
            'storage_path' => $disk === 'local'
                ? 'storage/app/'.$normalized
                : 'storage/app/public/'.$normalized,
            'status_note' => $statusNote,
            'size' => $size,
            'human_size' => $this->formatBytes($size),
            'uploader_name' => $uploaderName,
            'created_at' => $createdAt,
            'download_url' => $this->signedDiskDownloadUrl($disk, $normalized, $displayName),
            'is_image' => $isImage,
            'can_delete' => true,
            'delete_key' => 'orphan-'.$disk.'-'.$normalized,
        ];
    }

    /**
     * @param  array<string, mixed>  $oldValues
     * @return array{0: string, 1: ?string, 2: ?string}
     */
    protected function orphanRelatedFromHint(array $oldValues, ?object $hint): array
    {
        if ($oldValues === []) {
            return ['بدون مرجع در دیتابیس', null, null];
        }

        $attachableType = (string) ($oldValues['attachable_type'] ?? '');
        $attachableId = (int) ($oldValues['attachable_id'] ?? 0);
        $uploaderName = $hint?->user?->name ?? null;
        if (! $uploaderName && ! empty($oldValues['uploaded_by'])) {
            $uploaderName = \App\Models\User::withTrashed()->find((int) $oldValues['uploaded_by'])?->name;
        }

        if ($attachableType === ServiceOrder::class && $attachableId > 0) {
            $order = ServiceOrder::withTrashed()->with('customer:id,name')->find($attachableId);
            $label = 'سفارش تعمیر #'.$attachableId;
            if ($order?->trashed()) {
                $label .= ' (حذف‌شده)';
            } elseif ($order?->customer) {
                $label .= ' · '.$order->customer->name;
            }

            return [
                $label,
                $order && ! $order->trashed()
                    ? $this->safeRoute('automation.service-orders.show', ['serviceOrder' => $attachableId])
                    : null,
                $uploaderName,
            ];
        }

        return [
            class_basename($attachableType).' #'.$attachableId.' (رکورد حذف‌شده)',
            null,
            $uploaderName,
        ];
    }

    /**
     * @return Collection<string, object>
     */
    protected function deletedAttachmentHintsByBasename(): Collection
    {
        $hints = collect();

        ActivityLog::query()
            ->with('user:id,name')
            ->where('loggable_type', Attachment::class)
            ->whereIn('event', ['deleted', 'created'])
            ->latest()
            ->get()
            ->each(function (ActivityLog $log) use ($hints) {
                $values = $log->event === 'deleted'
                    ? ($log->old_values ?? [])
                    : ($log->new_values ?? []);

                $path = FileStorage::normalizePath((string) ($values['path'] ?? ''));
                $basename = basename($path);

                if ($basename === '' || $basename === '.' || $hints->has($basename)) {
                    return;
                }

                $hints->put($basename, (object) [
                    'old_values' => $values,
                    'user' => $log->user,
                    'created_at' => $log->created_at,
                    'event' => $log->event,
                ]);
            });

        return $hints;
    }

    protected function signedDiskDownloadUrl(string $disk, string $path, string $filename): string
    {
        return URL::signedRoute('admin.files.download', [
            'disk' => $disk,
            'path' => $path,
            'name' => $filename,
        ]);
    }

    protected function parseStorageFilenameTimestamp(string $basename): ?Carbon
    {
        if (preg_match('/^(\d{10})_/', $basename, $matches)) {
            return Carbon::createFromTimestamp((int) $matches[1]);
        }

        return null;
    }

    protected function mimeLabelForPath(string $path, string $disk): string
    {
        if ($path === '' || ! Storage::disk($disk)->exists($path)) {
            return 'نامشخص';
        }

        $fullPath = Storage::disk($disk)->path($path);
        $mime = @mime_content_type($fullPath) ?: 'application/octet-stream';

        return $this->mimeLabelFromType($mime);
    }

    protected function mimeLabelFromType(string $mime): string
    {
        return match (true) {
            str_starts_with($mime, 'image/') => 'تصویر · '.strtoupper(str_replace('image/', '', $mime)),
            $mime === 'application/pdf' => 'PDF',
            str_starts_with($mime, 'text/') => 'متن',
            str_contains($mime, 'word') => 'Word',
            str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet') => 'Excel',
            str_starts_with($mime, 'video/') => 'ویدیو',
            default => $mime !== 'application/octet-stream' ? $mime : 'فایل',
        };
    }

    protected function isImagePath(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
    }

    /**
     * @return array<string, true>
     */
    protected function referencedPublicPaths(): array
    {
        $paths = [];

        Product::withTrashed()->whereNotNull('images')->pluck('images')->each(function ($images) use (&$paths) {
            foreach ($images ?? [] as $path) {
                $normalized = FileStorage::normalizePath((string) $path);
                if ($normalized !== '') {
                    $paths[$normalized] = true;
                }
            }
        });

        Brand::withTrashed()->whereNotNull('logo')->pluck('logo')->each(function ($logo) use (&$paths) {
            $normalized = FileStorage::normalizePath((string) $logo);
            if ($normalized !== '') {
                $paths[$normalized] = true;
            }
        });

        return $paths;
    }

    protected function resolveAttachable(Attachment $file): ?Model
    {
        if (! $file->attachable_type || ! $file->attachable_id) {
            return null;
        }

        if (! class_exists($file->attachable_type)) {
            return null;
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $file->attachable_type;

        $query = $model->newQuery();
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model), true)) {
            $query->withTrashed();
        }

        return $query->find($file->attachable_id);
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    protected function relatedForAttachable(?string $type, ?int $id, $parent): array
    {
        if (! $type || ! $id) {
            return [null, null];
        }

        $deletedSuffix = $parent && method_exists($parent, 'trashed') && $parent->trashed()
            ? ' (حذف‌شده)'
            : '';

        if (! $parent) {
            $baseLabel = match ($type) {
                ServiceOrder::class => 'سفارش تعمیر #'.$id,
                Product::class => 'محصول #'.$id,
                Brand::class => 'برند #'.$id,
                default => class_basename($type).' #'.$id,
            };

            return [$baseLabel.' (مرجع حذف شده)', null];
        }

        return match ($type) {
            ServiceOrder::class => [
                'سفارش تعمیر #'.$id.$deletedSuffix,
                $parent->trashed() ? null : $this->safeRoute('automation.service-orders.show', ['serviceOrder' => $id]),
            ],
            Product::class => [
                'محصول: '.($parent->name ?? HashRef::plain($id)).$deletedSuffix,
                $parent->trashed() ? null : $this->safeRoute('admin.products.edit', ['product' => $id]),
            ],
            default => [
                class_basename($type).' #'.$id.$deletedSuffix,
                null,
            ],
        };
    }

    protected function publicFileSize(string $path): int
    {
        if ($path === '') {
            return 0;
        }

        if (Storage::disk('public')->exists($path)) {
            return (int) (Storage::disk('public')->size($path) ?: 0);
        }

        $full = public_path('storage/'.$path);

        return is_file($full) ? (int) filesize($full) : 0;
    }

    protected function publicFileMtime(string $path): ?Carbon
    {
        if ($path === '') {
            return null;
        }

        $timestamp = null;
        if (Storage::disk('public')->exists($path)) {
            $timestamp = Storage::disk('public')->lastModified($path);
        } else {
            $full = public_path('storage/'.$path);
            if (is_file($full)) {
                $timestamp = filemtime($full);
            }
        }

        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }

    protected function privateFileMtime(string $path): ?Carbon
    {
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $timestamp = Storage::disk('local')->lastModified($path);

        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $value = max(0, $bytes);

        for ($i = 0; $value > 1024 && $i < count($units) - 1; $i++) {
            $value /= 1024;
        }

        return round($value, 2).' '.$units[$i];
    }

    protected function safeRoute(string $name, array $parameters = []): ?string
    {
        try {
            return route($name, $parameters);
        } catch (\Throwable) {
            return null;
        }
    }
}
