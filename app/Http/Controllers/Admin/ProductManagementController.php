<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Inventory;
use App\Services\FileStorage;
use App\Services\ProductActivityLogger;
use App\Services\ProductActivityTimeline;
use App\Services\ShopInventorySync;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVersion;
use Illuminate\Http\Request;
use App\Support\ShopFormat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class ProductManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withTrashed()->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') $query->where('is_active', true);
            if ($request->status === 'inactive') $query->where('is_active', false);
            if ($request->status === 'deleted') $query->onlyTrashed();
        }

        $products = $query->latest()->paginate(15);
        $categories = ProductCategory::optionsForSelect();
        $categoryPaths = ProductCategory::optionPathsForSelect();

        return view('admin.products.index', compact('products', 'categories', 'categoryPaths'));
    }

    public function create()
    {
        $categories = ProductCategory::optionsForSelect();
        $categoryPaths = ProductCategory::optionPathsForSelect();
        $inventories = Inventory::select('id', 'name', 'sku', 'quantity', 'device_code', 'price')
            ->latest()
            ->get()
            ->map(function ($inv) {
                $inv->taken_by_other = Product::where('inventory_id', $inv->id)->exists();

                return $inv;
            });

        return view('admin.products.create', compact('categories', 'categoryPaths', 'inventories'));
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $productData = $validated;
        unset($productData['images'], $productData['has_discount']);
        $productData['technical_specs'] = ShopFormat::normalizeTechnicalSpecs($request->input('technical_specs'));
        $productData = $this->normalizeProductData($productData, $request, null);
        
        // Handle images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = FileStorage::storePublic($image, 'products');
            }
        }
        $productData['images'] = $imagePaths;
        $productData['slug'] = ShopFormat::uniqueSlug($request->name, Product::class);

        $product = Product::create($productData);
        $product = $product->fresh();
        ShopInventorySync::syncProductFromInventory($product);

        if ($product->inventory_id) {
            $inventory = Inventory::find($product->inventory_id);
            ProductActivityLogger::log(
                $product,
                'inventory_linked',
                'اتصال به انبار',
                $inventory ? "کالای انبار: {$inventory->name}" : null,
                null,
                (int) $product->stock_quantity,
            );
        }
        
        // Initial version
        $product->createVersion('محصول ایجاد شد');

        return Redirect::route('admin.products.index')->with('success', 'محصول با موفقیت ایجاد شد.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::optionsForSelect();
        $categoryPaths = ProductCategory::optionPathsForSelect();
        $inventories = Inventory::select('id', 'name', 'sku', 'quantity', 'device_code', 'price')
            ->latest()
            ->get()
            ->map(function ($inv) use ($product) {
                $inv->taken_by_other = Product::where('inventory_id', $inv->id)
                    ->where('id', '!=', $product->id)
                    ->exists();

                return $inv;
            });
        $suggestedInventory = null;
        if (! $product->inventory_id && $product->sku) {
            $suggestedInventory = Inventory::where('sku', $product->sku)->first();
        }
        $recentActivities = ProductActivityTimeline::forProduct($product, 10);
        $linkedInventory = $product->inventory_id ? Inventory::find($product->inventory_id) : null;

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'categoryPaths',
            'inventories',
            'suggestedInventory',
            'recentActivities',
            'linkedInventory',
        ));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $productData = $validated;
        unset($productData['images'], $productData['change_reason'], $productData['has_discount']);
        $productData['technical_specs'] = ShopFormat::normalizeTechnicalSpecs($request->input('technical_specs'));
        $productData = $this->normalizeProductData($productData, $request, $product);

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images from storage
            if ($product->images && is_array($product->images)) {
                foreach ($product->images as $oldImageUrl) {
                    FileStorage::deletePublic($oldImageUrl);
                }
            }

            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = FileStorage::storePublic($image, 'products');
            }
            $productData['images'] = $imagePaths;
        }

        $oldInventoryId = $product->inventory_id;
        $oldStockQuantity = (int) $product->stock_quantity;

        $product->update($productData);
        $product = $product->fresh();
        ShopInventorySync::syncProductFromInventory($product);

        if (! $product->inventory_id && $oldStockQuantity !== (int) $product->stock_quantity) {
            ProductActivityLogger::logShopOnly(
                $product,
                'inventory_adjust',
                'تغییر موجودی فروشگاه',
                'ویرایش دستی — مستقل از انبار',
                (int) $product->stock_quantity - $oldStockQuantity,
                (int) $product->stock_quantity,
            );
        }

        if ($product->inventory_id && (int) $oldInventoryId !== (int) $product->inventory_id) {
            $inventory = Inventory::find($product->inventory_id);
            ProductActivityLogger::log(
                $product,
                'inventory_linked',
                'اتصال به انبار',
                $inventory ? "کالای انبار: {$inventory->name} — موجودی همگام: {$product->stock_quantity}" : null,
                null,
                (int) $product->stock_quantity,
            );
        } elseif ($oldInventoryId && ! $product->inventory_id) {
            ProductActivityLogger::log(
                $product,
                'inventory_unlinked',
                'قطع اتصال انبار',
                'موجودی فروشگاه مستقل از انبار مدیریت می‌شود.',
                null,
                (int) $product->stock_quantity,
            );
        }

        ProductActivityLogger::log(
            $product,
            'product_edit',
            'ویرایش محصول',
            $request->change_reason ?? 'بروزرسانی از پنل',
            null,
            (int) $product->stock_quantity,
        );
        
        // Create new version
        $product->createVersion($request->change_reason ?? 'بروزرسانی محصول');

        return Redirect::route('admin.products.index')->with('success', 'محصول با موفقیت بروزرسانی شد.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'محصول به سطل زباله منتقل شد.');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return back()->with('success', 'محصول بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        // Delete images from storage
        if ($product->images) {
            foreach ($product->images as $imageUrl) {
                FileStorage::deletePublic($imageUrl);
            }
        }
        $product->forceDelete();
        return back()->with('success', 'محصول کاملاً حذف شد.');
    }

    public function history(Product $product)
    {
        $versions = $product->versions()->with('user')->latest()->get();
        $activities = ProductActivityTimeline::forProduct($product, 200);

        return view('admin.products.history', compact('product', 'versions', 'activities'));
    }

    public function export()
    {
        $products = Product::with('category')->get();
        $filename = "products_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Name EN', 'SKU', 'Category', 'Price', 'Sale Price', 'Stock'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->name_en,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->price,
                    $product->sale_price,
                    $product->stock_quantity,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function syncInventory(Request $request, Product $product)
    {
        $inventoryId = (int) ($request->input('inventory_id') ?: $product->inventory_id);

        if ($inventoryId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ابتدا یک کالا از انبار انتخاب کنید.',
            ], 422);
        }

        $inventory = Inventory::find($inventoryId);
        if (! $inventory) {
            return response()->json([
                'success' => false,
                'message' => 'کالای انبار یافت نشد.',
            ], 404);
        }

        $alreadyLinkedElsewhere = Product::where('inventory_id', $inventoryId)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($alreadyLinkedElsewhere) {
            return response()->json([
                'success' => false,
                'message' => 'این کالای انبار قبلاً به محصول دیگری متصل شده است.',
            ], 422);
        }

        $wasLinked = (bool) $product->inventory_id;
        $previousInventoryId = $product->inventory_id;

        $product->update([
            'inventory_id' => $inventoryId,
            'manage_stock' => true,
        ]);

        ShopInventorySync::syncProductFromInventory($product, quiet: false);
        $product = $product->fresh();

        if (! $wasLinked || (int) $previousInventoryId !== $inventoryId) {
            ProductActivityLogger::log(
                $product,
                'inventory_linked',
                'اتصال و هماهنگ‌سازی با انبار',
                "کالای انبار: {$inventory->name} — موجودی: {$product->stock_quantity}",
                null,
                (int) $product->stock_quantity,
            );
        } else {
            ProductActivityLogger::log(
                $product,
                'inventory_sync',
                'هماهنگ‌سازی با انبار',
                "موجودی انبار: {$inventory->quantity}",
                null,
                (int) $product->stock_quantity,
            );
        }

        return response()->json([
            'success' => true,
            'linked' => true,
            'message' => $wasLinked ? 'موجودی فروشگاه با انبار هماهنگ شد.' : 'محصول به انبار متصل و هماهنگ شد.',
            'inventory_id' => $product->inventory_id,
            'stock_quantity' => (int) $product->stock_quantity,
            'inventory_quantity' => (int) $inventory->quantity,
            'inventory_price' => (int) round((float) $inventory->price),
        ]);
    }

    public function detachInventory(Product $product)
    {
        if (! $product->inventory_id) {
            return response()->json([
                'success' => false,
                'message' => 'این محصول از قبل جدا از انبار است.',
            ], 422);
        }

        $stockQuantity = (int) $product->stock_quantity;
        $product->update([
            'inventory_id' => null,
            'manage_stock' => true,
        ]);

        ProductActivityLogger::log(
            $product->fresh(),
            'inventory_unlinked',
            'جداسازی از انبار',
            'موجودی فروشگاه مستقل از انبار مدیریت می‌شود.',
            null,
            $stockQuantity,
        );

        return response()->json([
            'success' => true,
            'message' => 'محصول از انبار جدا شد. موجودی فروشگاه مستقل است.',
            'stock_quantity' => $stockQuantity,
        ]);
    }

    public function markOutOfStock(Product $product)
    {
        if ($product->inventory_id) {
            $inventory = Inventory::find($product->inventory_id);
            if ($inventory && (int) $inventory->quantity > 0) {
                $inventory->updateStock(
                    - (int) $inventory->quantity,
                    'adjustment',
                    'اتمام موجودی از پنل محصولات: '.$product->name
                );
            }
            ShopInventorySync::syncProductFromInventory($product->fresh());
            ProductActivityLogger::log(
                $product->fresh(),
                'out_of_stock',
                'اتمام موجودی',
                'ثبت از پنل محصولات (همگام با انبار)',
                null,
                0,
            );
        } else {
            $product->update([
                'stock_quantity' => 0,
                'stock_status' => 'outofstock',
            ]);
            ProductActivityLogger::log($product->fresh(), 'out_of_stock', 'اتمام موجودی', null, null, 0);
        }

        return back()->with('success', 'موجودی محصول «'.$product->name.'» به اتمام رسید.');
    }

    private function normalizeProductData(array $data, ProductRequest $request, ?Product $product = null): array
    {
        $data['price'] = max(0, (float) ($data['price'] ?? 0));

        if ($request->boolean('has_discount')) {
            $data['sale_price'] = max(0, (float) ($data['sale_price'] ?? 0));
            if ($data['sale_price'] <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'sale_price' => 'قیمت با تخفیف را وارد کنید.',
                ]);
            }
        } else {
            $data['sale_price'] = 0.0;
        }

        $data['manage_stock'] = true;

        unset($data['inventory_linked']);

        if ($product) {
            $product->refresh();

            if ($product->inventory_id) {
                $data['inventory_id'] = $product->inventory_id;
                $data = ShopInventorySync::applyInventoryMaster($data);
            } else {
                $data['inventory_id'] = null;
                $data['stock_status'] = ($data['stock_quantity'] ?? 0) <= 0 ? 'outofstock' : 'instock';
            }

            return $data;
        }

        if ($request->boolean('inventory_linked') && ! empty($data['inventory_id'])) {
            $data = ShopInventorySync::applyInventoryMaster($data);
        } else {
            $data['inventory_id'] = null;
            $data['stock_status'] = ($data['stock_quantity'] ?? 0) <= 0 ? 'outofstock' : 'instock';
        }

        return $data;
    }
}
