<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category.parent', 'brand']);

        // Filtering
        $rawCategory = $request->input('category');
        if (is_array($rawCategory)) {
            $categorySelection = array_values(array_filter($rawCategory, fn ($value) => $value !== '' && $value !== null));
        } elseif ($request->filled('category')) {
            $categorySelection = [(string) $rawCategory];
        } else {
            $categorySelection = [];
        }
        if ($categorySelection !== []) {
            $categoryIds = ProductCategory::resolveFilterSelectionToCategoryIds($categorySelection);
            if ($categoryIds !== []) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->has('in_stock')) {
            $query->where('stock_status', 'instock')->where('stock_quantity', '>', 0);
        } elseif (! $request->boolean('show_all')) {
            $query->where('stock_status', 'instock')->where('stock_quantity', '>', 0);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->has('new')) {
            $query->new();
        }

        if ($request->boolean('on_sale')) {
            $query->onSale();
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($qb) use ($search) {
                $qb->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhereHas('parent', fn ($pq) => $pq->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('parent.parent', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categoryTree = ProductCategory::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['children' => fn ($q2) => $q2->where('is_active', true)->orderBy('sort_order')])])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact('products', 'categoryTree'));
    }

    public function category(ProductCategory $category, Request $request)
    {
        // Reuse index logic but with fixed category
        $request->merge(['category' => (string) $category->id]);
        return $this->index($request);
    }

    public function show(Product $product)
    {
        $product->increment('view_count');
        $product->load(['category', 'brand']);

        $relatedProducts = Product::active()
            ->with(['category', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }
}
