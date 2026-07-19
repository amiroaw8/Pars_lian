<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Support\ShopFormat;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::with([
            'children' => fn ($q) => $q->with(['children' => fn ($q2) => $q2->withCount('products')])->withCount('products'),
        ])->withCount('products');

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $categories = $query->whereNull('parent_id')->ordered()->get();
        $parentOptions = ProductCategory::optionsForSelect();
        $parentOptionPaths = ProductCategory::optionPathsForSelect();
        $treeProductCounts = ProductCategory::treeProductCountMap();

        return view('admin.categories.index', compact('categories', 'parentOptions', 'parentOptionPaths', 'treeProductCounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        $this->assertValidParent($validated['parent_id'] ?? null);

        $validated['slug'] = ShopFormat::uniqueSlug($validated['name'], ProductCategory::class);

        ProductCategory::create($validated);

        \Illuminate\Support\Facades\Cache::forget('shop_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        if (! empty($validated['parent_id']) && (int) $validated['parent_id'] === (int) $category->id) {
            throw ValidationException::withMessages(['parent_id' => 'دسته‌بندی نمی‌تواند والد خودش باشد.']);
        }

        $this->assertValidParent($validated['parent_id'] ?? null, $category->id);

        $validated['slug'] = ShopFormat::uniqueSlug($validated['name'], ProductCategory::class, $category->id);

        $category->update($validated);

        \Illuminate\Support\Facades\Cache::forget('shop_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت بروزرسانی شد.');
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'این دسته‌بندی دارای زیردسته است و قابل حذف نیست.');
        }

        if ($category->products()->exists()) {
            return back()->with('error', 'این دسته‌بندی دارای محصول است و قابل حذف نیست.');
        }

        $category->delete();

        \Illuminate\Support\Facades\Cache::forget('shop_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی به سطل زباله منتقل شد.');
    }

    public function restore($id)
    {
        $category = ProductCategory::withTrashed()->findOrFail($id);
        $category->restore();

        \Illuminate\Support\Facades\Cache::forget('shop_categories');

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'دسته‌بندی با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $category = ProductCategory::withTrashed()->findOrFail($id);

        if ($category->children()->exists() || $category->products()->exists()) {
            return back()->with('error', 'این دسته‌بندی دارای زیردسته یا محصول است و قابل حذف دائمی نیست.');
        }

        $category->forceDelete();

        \Illuminate\Support\Facades\Cache::forget('shop_categories');

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'دسته‌بندی برای همیشه حذف شد.');
    }

    public function storeQuick(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name',
                'parent_id' => 'nullable|exists:product_categories,id',
            ]);

            $this->assertValidParent($validated['parent_id'] ?? null);

            $validated['slug'] = ShopFormat::uniqueSlug($validated['name'], ProductCategory::class);

            $category = ProductCategory::create($validated);

            \Illuminate\Support\Facades\Cache::forget('shop_categories');

            return response()->json([
                'success' => true,
                'category' => $category,
                'label' => ProductCategory::optionsForSelect()[$category->id] ?? $category->name,
                'path' => ProductCategory::optionPathsForSelect()[$category->id] ?? $category->name,
                'can_have_children' => $category->canHaveChildren(),
                'message' => 'دسته‌بندی با موفقیت ایجاد شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    private function assertValidParent(?int $parentId, ?int $excludeCategoryId = null): void
    {
        if (! $parentId) {
            return;
        }

        if ($excludeCategoryId && $parentId === $excludeCategoryId) {
            throw ValidationException::withMessages(['parent_id' => 'دسته‌بندی نمی‌تواند والد خودش باشد.']);
        }

        $parent = ProductCategory::find($parentId);
        if (! $parent) {
            return;
        }

        if ($parent->depth() >= 3) {
            throw ValidationException::withMessages(['parent_id' => 'حداکثر عمق دسته‌بندی سه سطح است.']);
        }

        if ($excludeCategoryId) {
            $cursor = $parent;
            while ($cursor) {
                if ((int) $cursor->id === $excludeCategoryId) {
                    throw ValidationException::withMessages(['parent_id' => 'انتخاب این والد باعث حلقه در درخت می‌شود.']);
                }
                $cursor = $cursor->parent;
            }
        }
    }
}
