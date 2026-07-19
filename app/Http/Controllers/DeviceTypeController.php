<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DeviceType;
use App\Http\Requests\DeviceTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class DeviceTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceType::with('childrenRecursive')
            ->whereNull('parent_id');

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $deviceTypes = $query->orderBy('name')->get();
        $parentOptions = DeviceType::optionsForSelect();
        $parentExcludeMap = DeviceType::parentExcludeMap();

        return view('device-types.index', compact('deviceTypes', 'parentOptions', 'parentExcludeMap'));
    }

    public function store(DeviceTypeRequest $request)
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isWarehouseManager())) {
            abort(403, 'شما اجازه اضافه کردن نوع دستگاه را ندارید.');
        }

        DeviceType::create($request->validated());

        Cache::forget('device_types_hierarchy');

        return Redirect::route('automation.device-types.index')
            ->with('success', 'نوع دستگاه با موفقیت اضافه شد.');
    }

    public function update(DeviceTypeRequest $request, DeviceType $deviceType)
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isWarehouseManager())) {
            abort(403, 'شما اجازه ویرایش نوع دستگاه را ندارید.');
        }

        $deviceType->update($request->validated());

        Cache::forget('device_types_hierarchy');

        return Redirect::route('automation.device-types.index')
            ->with('success', 'نوع دستگاه با موفقیت ویرایش شد.');
    }

    public function destroy(DeviceType $deviceType)
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isWarehouseManager())) {
            abort(403, 'شما اجازه حذف نوع دستگاه را ندارید.');
        }

        if ($deviceType->children()->exists()) {
            return Redirect::back()->with('error', 'این نوع دستگاه دارای زیرمجموعه است و قابل حذف نیست.');
        }

        $deviceType->delete();

        Cache::forget('device_types_hierarchy');

        return Redirect::route('automation.device-types.index')
            ->with('success', 'نوع دستگاه حذف شد.');
    }

    public function restore($id)
    {
        $deviceType = DeviceType::withTrashed()->findOrFail($id);
        $deviceType->restore();

        Cache::forget('device_types_hierarchy');

        return Redirect::route('automation.device-types.index', ['trashed' => 1])
            ->with('success', 'نوع دستگاه با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'فقط سوپر ادمین می‌تواند نوع دستگاه را برای همیشه حذف کند.');
        }

        $deviceType = DeviceType::withTrashed()->findOrFail($id);
        
        if ($deviceType->children()->exists()) {
            return Redirect::back()->with('error', 'این نوع دستگاه دارای زیرمجموعه است و قابل حذف دائمی نیست.');
        }

        $deviceType->forceDelete();

        Cache::forget('device_types_hierarchy');

        return Redirect::route('automation.device-types.index', ['trashed' => 1])
            ->with('success', 'نوع دستگاه برای همیشه حذف شد.');
    }
}
