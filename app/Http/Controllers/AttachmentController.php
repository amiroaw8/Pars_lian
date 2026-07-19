<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\ServiceOrder;
use App\Services\FileStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function preview(Attachment $attachment): StreamedResponse
    {
        $this->authorizeAccess($attachment);

        if (! str_starts_with((string) $attachment->mime_type, 'image/')) {
            abort(404);
        }

        [$disk, $storedPath] = $this->resolveDiskAndPath($attachment);

        return $disk->response($storedPath, $attachment->name, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($attachment->name).'"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function download(Attachment $attachment)
    {
        $this->authorizeAccess($attachment);

        [$disk, $storedPath] = $this->resolveDiskAndPath($attachment);

        return $disk->download($storedPath, $attachment->name);
    }

    public function destroy(Request $request, Attachment $attachment)
    {
        if ($attachment->uploaded_by !== Auth::id() && ! Auth::user()->canAccessAdminPanel()) {
            return back()->with('error', 'شما اجازه حذف این فایل را ندارید.');
        }

        Log::info("فایل '{$attachment->name}' توسط کاربر ".Auth::user()->name.' به سطل زباله منتقل شد', [
            'attachment_id' => $attachment->id,
            'file_name' => $attachment->name,
            'user_id' => Auth::id(),
        ]);

        $attachment->delete();

        $redirect = back()->with('success', 'فایل به سطل زباله منتقل شد.');
        $scrollTo = $request->input('scroll_to');

        if (is_string($scrollTo) && $scrollTo !== '') {
            $redirect = $redirect->with('scroll_to', $scrollTo);
        }

        return $redirect;
    }

    private function authorizeAccess(Attachment $attachment): void
    {
        $user = Auth::user();
        if (! $user->isEmployee() && ! $user->hasRole('customer')) {
            abort(403);
        }

        if (! $user->hasRole('customer')) {
            return;
        }

        $canAccess = false;
        if ($attachment->attachable_type === ServiceOrder::class) {
            $order = $attachment->attachable;
            if ($order && $order->customer_id === $user->customer?->id) {
                $canAccess = true;
            }
        }

        if (! $canAccess) {
            abort(403, 'شما اجازه دسترسی به این فایل را ندارید.');
        }
    }

    /**
     * @return array{0: FilesystemAdapter, 1: string}
     */
    private function resolveDiskAndPath(Attachment $attachment): array
    {
        $storedPath = FileStorage::normalizePath($attachment->path);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        if (! $disk->exists($storedPath)) {
            $disk = Storage::disk('local');
            if (! $disk->exists($storedPath)) {
                abort(404, 'فایل یافت نشد.');
            }
        }

        return [$disk, $storedPath];
    }
}
