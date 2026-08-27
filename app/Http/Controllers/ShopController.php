<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()
            ->inStock()
            ->with('category')
            ->filter($request);

        // Custom logic for price range since it's not a direct column match
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Custom logic for category slug (Filterable uses ID by default)
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $query->when($sortBy, function ($q, $sortBy) {
            match ($sortBy) {
                'price_asc' => $q->orderBy('price', 'asc'),
                'price_desc' => $q->orderBy('price', 'desc'),
                'newest' => $q->orderBy('created_at', 'desc'),
                'popular' => $q->orderBy('view_count', 'desc'),
                default => $q->orderBy('name', 'asc'),
            };
        });

        $products = $query->paginate(12)->appends($request->query());

        // Get categories for sidebar
        $categories = Cache::remember('shop_categories', config('settings.shop_cache_ttl', 3600), function () {
            return ProductCategory::active()->parents()->ordered()->with(['children' => function ($q) {
                $q->active()->ordered();
            }])->get();
        });

        // Get featured products
        $featuredProducts = Cache::remember('featured_products', config('settings.featured_products_cache_ttl', 1800), function () {
            return Product::featured()->active()->inStock()->with('category')->take(4)->get();
        });

        return view('shop.index', compact('products', 'categories', 'featuredProducts'));
    }

    public function show(Product $product)
    {
        // Increment view count
        $product->incrementViewCount();

        // Load category for breadcrumb and other info
        $product->load('category');

        // Get related products
        $relatedProducts = Product::active()
            ->inStock()
            ->with('category')
            ->byCategory($product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // Get product categories breadcrumb
        $categories = collect();
        $category = $product->category;
        while ($category) {
            $categories->prepend($category);
            $category = $category->parent;
        }

        return view('shop.show', compact('product', 'relatedProducts', 'categories'));
    }

    public function category(ProductCategory $category)
    {
        $products = Product::active()
            ->inStock()
            ->byCategory($category->id)
            ->paginate(12);

        // Get subcategories
        $subcategories = $category->children()->active()->get();

        // Get category path for breadcrumb
        $categoryPath = collect();
        $current = $category;
        while ($current) {
            $categoryPath->prepend($current);
            $current = $current->parent;
        }

        return view('shop.category', compact('category', 'products', 'subcategories', 'categoryPath'));
    }

    public function search(Request $request)
    {
        $rawQuery = $request->get('q', '');
        $query = is_string($rawQuery) ? trim($rawQuery) : '';

        // If query is empty, only spaces, or matches unparsed JS template string like ${s}
        if ($query === '' || preg_match('/^\$\{[a-zA-Z0-9_]*\}$/', $query)) {
            return redirect()->route('shop.index');
        }

        return redirect()->route('catalog.index', ['q' => $query]);
    }

    public function tracking(Request $request)
    {
        $serviceOrder = null;
        $shopOrder = null;
        $error = null;
        $trackingType = null;

        if ($request->filled('tracking_id') && $request->filled('phone')) {
            $trackingId = trim((string) $request->tracking_id);
            $phone = preg_replace('/\D+/', '', (string) $request->phone);
            $phoneTail = strlen($phone) >= 10 ? substr($phone, -10) : $phone;

            $serviceOrder = \App\Models\ServiceOrder::with(['customer', 'device', 'orderLogs' => fn ($q) => $q->latest()])
                ->where('id', $trackingId)
                ->where(function ($q) use ($phone, $phoneTail) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(receiver_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$phoneTail}%"])
                      ->orWhereHas('customer', fn ($c) => $c->whereRaw("REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$phoneTail}%"]));
                })
                ->first();

            if ($serviceOrder) {
                $trackingType = 'service';
            } else {
                $shopOrder = \App\Models\Order::with('items.product')
                    ->where(function ($q) use ($trackingId) {
                        $q->where('order_number', $trackingId)
                          ->orWhere('id', $trackingId);
                    })
                    ->whereRaw("REPLACE(REPLACE(REPLACE(shipping_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$phoneTail}%"])
                    ->first();

                if ($shopOrder) {
                    $trackingType = 'shop';
                }
            }

            if (! $serviceOrder && ! $shopOrder) {
                $error = 'اطلاعات وارد شده صحیح نیست یا سفارشی با این مشخصات یافت نشد.';
            }
        } elseif ($request->isMethod('get') && ($request->filled('tracking_id') || $request->filled('phone'))) {
            $error = 'لطفاً هر دو مورد شناسه پیگیری و شماره همراه را وارد کنید.';
        }

        return view('shop.tracking', compact('serviceOrder', 'shopOrder', 'error', 'trackingType'));
    }

    public function about()
    {
        return view('shop.about');
    }

    public function contact()
    {
        return view('shop.contact');
    }
}
