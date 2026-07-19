<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests\InventoryRequest;
use App\Http\Requests\InventoryStockRequest;
use App\Http\Requests\InventoryAdjustmentRequest;
use App\Services\ShopInventorySync;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Only managers can access inventory index
        if (!Auth::user()->canManageInventory()) {
            Log::warning('Unauthorized access attempt to inventory section', [
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);
            abort(403, 'شما اجازه دسترسی به بخش انبار را ندارید.');
        }

        $query = Inventory::query();

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('device_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting Logic
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSorts = ['name', 'sku', 'device_code', 'type', 'condition', 'quantity', 'min_quantity', 'price', 'color', 'created_at'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $inventories = $query->orderBy($sortField, $sortDirection)
            ->paginate(25)
            ->withQueryString();

        return view('inventory.index', compact('inventories'));
    }

    public function create()
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
        }

        return view('inventory.create');
    }

    public function store(InventoryRequest $request)
    {
        // Check permissions
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه اضافه کردن کالا به انبار را ندارید.');
        }

        Inventory::create($request->validated());

        return Redirect::route('automation.inventory.index')
            ->with('success', 'کالا با موفقیت اضافه شد.');
    }

    public function show(Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه مشاهده جزئیات انبار را ندارید.');
        }

        $transactions = $inventory->transactions()
            ->latest()
            ->paginate(20);

        $linkedProducts = $inventory->products()->get(['id', 'name', 'sku', 'stock_quantity', 'stock_status']);
        $stockMismatches = $linkedProducts->filter(
            fn ($p) => (int) $p->stock_quantity !== (int) $inventory->quantity
        );

        return view('inventory.show', compact('inventory', 'transactions', 'linkedProducts', 'stockMismatches'));
    }

    public function edit(Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه ویرایش کالای انبار را ندارید.');
        }

        return view('inventory.edit', compact('inventory'));
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه ویرایش کالای انبار را ندارید.');
        }

        $inventory->update($request->validated());

        return Redirect::route('automation.inventory.index')
            ->with('success', 'کالا با موفقیت ویرایش شد.');
    }

    public function destroy(Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه حذف کالای انبار را ندارید.');
        }

        $inventory->delete();

        return Redirect::route('automation.inventory.index')
            ->with('success', 'کالا با موفقیت حذف شد.');
    }

    public function restore($id)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه بازیابی کالای انبار را ندارید.');
        }

        $inventory = Inventory::withTrashed()->findOrFail($id);
        $inventory->restore();

        return Redirect::route('automation.inventory.index', ['trashed' => 1])
            ->with('success', 'کالا با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه حذف دائمی کالای انبار را ندارید.');
        }

        $inventory = Inventory::withTrashed()->findOrFail($id);
        $inventory->forceDelete();

        return Redirect::route('automation.inventory.index', ['trashed' => 1])
            ->with('success', 'کالا برای همیشه از دیتابیس حذف شد.');
    }

    public function updateStock(InventoryStockRequest $request, Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه تغییر موجودی انبار را ندارید.');
        }

        Log::info('Inventory update attempt', [
            'inventory_id' => $inventory->id,
            'quantity_change' => $request->quantity_change,
            'type' => $request->transaction_type
        ]);

        try {
            $quantity = $request->quantity_change;

            $finalChange = match ($request->transaction_type) {
                'purchase', 'return', 'warranty_return' => +$quantity,
                'sale', 'use', 'warranty_sent' => -$quantity,
                default => null,
            };

            if ($finalChange === null) {
                return Redirect::back()->with('error', 'نوع تراکنش نامعتبر است.');
            }

            $inventory->updateStock(
                $finalChange,
                $request->transaction_type,
                $request->notes ?? '',
                $request->only(['receiver', 'organization', 'reason'])
            );

            Log::info('Inventory update successful', ['new_quantity' => $inventory->quantity]);

            return Redirect::back()->with('success', 'موجودی با موفقیت به روز شد.');
        } catch (\RuntimeException $e) {
            // Business Exception from Inventory model (e.g. not enough stock)
            Log::warning('Inventory update business logic failed', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Inventory update failed structurally', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'خطای سیستمی در بروزرسانی. لطفاً لاگ سیستم را بررسی کنید.');
        }
    }

    public function adjustStock(InventoryAdjustmentRequest $request, Inventory $inventory)
    {
        if (!Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه تعدیل موجودی انبار را ندارید.');
        }

        try {
            $difference = $request->new_quantity - $inventory->quantity;

            $inventory->updateStock(
                $difference,
                'adjustment',
                ($request->notes ?? '') . " (تعدیل به: {$request->new_quantity})"
            );

            return Redirect::back()->with('success', 'تعدیل انبار با موفقیت ثبت شد.');
        } catch (\RuntimeException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Inventory adjust failed structurally', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'خطای سیستمی رخ داده است. لطفاً مدیریت را مطلع کنید.');
        }
    }

    public function syncShopProducts(Inventory $inventory)
    {
        if (! Auth::user()->canManageInventory()) {
            abort(403, 'شما اجازه همگام‌سازی انبار را ندارید.');
        }

        $result = ShopInventorySync::reconcile($inventory->id);

        $message = $result['synced'] > 0
            ? "همگام‌سازی انجام شد؛ {$result['synced']} محصول فروشگاه به‌روز شد."
            : 'همه محصولات متصل از قبل با انبار هماهنگ بودند.';

        return Redirect::back()->with('success', $message);
    }
}
