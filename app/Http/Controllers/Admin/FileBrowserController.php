<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileStorage;
use App\Services\SiteFileCatalog;
use App\Services\SiteFileDeleter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileBrowserController extends Controller
{
    public function index(SiteFileCatalog $catalog)
    {
        $grouped = $catalog->grouped();
        $stats = $catalog->stats();

        return view('admin.files.index', compact('grouped', 'stats'));
    }

    public function download(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'لینک دانلود نامعتبر یا منقضی شده است.');
        }

        $disk = (string) $request->query('disk', 'local');
        $path = FileStorage::normalizePath((string) $request->query('path', ''));
        $filename = (string) $request->query('name', basename($path));

        if ($path === '' || ! in_array($disk, ['local', 'public'], true)) {
            abort(404);
        }

        $allowedPrefixes = match ($disk) {
            'local' => ['attachments/'],
            'public' => ['products/', 'brand/'],
            default => [],
        };

        $allowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed || ! Storage::disk($disk)->exists($path)) {
            abort(404, 'فایل یافت نشد.');
        }

        return Storage::disk($disk)->download($path, $filename);
    }

    public function destroy(Request $request, SiteFileDeleter $deleter)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:500'],
        ]);

        try {
            $message = $deleter->deleteByKey($validated['key']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.files.index')
            ->with('success', $message);
    }

    public static function categoryLabel(?string $type): string
    {
        return SiteFileCatalog::CATEGORY_LABELS[$type ?? ''] ?? 'سایر فایل‌ها';
    }
}
