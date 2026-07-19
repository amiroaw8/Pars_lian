<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Customer;
use App\Http\Requests\DeviceRequest;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('customer');

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('asset_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->latest()->paginate(20);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('devices.create', compact('customers'));
    }

    public function store(DeviceRequest $request)
    {
        Device::create($request->validated());

        return redirect()->route('automation.devices.index')
            ->with('success', 'دستگاه با موفقیت ایجاد شد.');
    }

    public function show(Device $device)
    {
        return view('devices.show', compact('device'));
    }

    public function edit(Device $device)
    {
        $customers = Customer::orderBy('name')->get();
        return view('devices.edit', compact('device', 'customers'));
    }

    public function update(DeviceRequest $request, Device $device)
    {
        $device->update($request->validated());

        return redirect()->route('automation.devices.index')
            ->with('success', 'دستگاه با موفقیت ویرایش شد.');
    }

    public function destroy(Device $device)
    {
        if ($device->serviceOrders()->exists()) {
            return back()->with('error', 'این دستگاه دارای سفارش خدمات است و قابل حذف نیست.');
        }

        $device->delete();

        return redirect()->route('automation.devices.index')
            ->with('success', 'دستگاه با موفقیت حذف شد.');
    }

    public function restore($id)
    {
        $device = Device::withTrashed()->findOrFail($id);
        $device->restore();

        return redirect()->route('automation.devices.index', ['trashed' => 1])
            ->with('success', 'دستگاه با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $device = Device::withTrashed()->findOrFail($id);
        
        if ($device->serviceOrders()->exists()) {
            return back()->with('error', 'این دستگاه دارای سفارش خدمات است و قابل حذف دائمی نیست.');
        }

        $device->forceDelete();

        return redirect()->route('automation.devices.index', ['trashed' => 1])
            ->with('success', 'دستگاه برای همیشه حذف شد.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $devices = Device::where('model', 'like', "%$query%")
            ->orWhere('type', 'like', "%$query%")
            ->with('customer')
            ->limit(10)
            ->get();

        return response()->json($devices);
    }
}
