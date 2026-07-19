<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\RecycleBinRegistry;

class RecycleBinController extends Controller
{
    public function index()
    {
        $deletedItems = RecycleBinRegistry::deletedItems();

        return view('admin.recycle-bin.index', compact('deletedItems'));
    }

    public function restore($type, $id)
    {
        $model = RecycleBinRegistry::modelClass($type);
        if (! $model) {
            return back()->with('error', 'نوع داده نامعتبر است.');
        }

        $item = $model::withTrashed()->find($id);
        if ($item) {
            $item->restore();

            return back()->with('success', 'آیتم با موفقیت بازیابی شد.');
        }

        return back()->with('error', 'آیتم یافت نشد.');
    }

    public function forceDelete($type, $id)
    {
        $model = RecycleBinRegistry::modelClass($type);
        if (! $model) {
            return back()->with('error', 'نوع داده نامعتبر است.');
        }

        $item = $model::withTrashed()->find($id);
        if ($item) {
            RecycleBinRegistry::runBeforeForceDelete($type, $item);
            $item->forceDelete();

            return back()->with('success', 'آیتم به صورت دائمی حذف شد.');
        }

        return back()->with('error', 'آیتم یافت نشد.');
    }
}
