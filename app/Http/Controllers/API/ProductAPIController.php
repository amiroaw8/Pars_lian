<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductAPIController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::active()
            ->with('category')
            ->filter($request)
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($products);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product->load('category'));
    }

    public function store(Request $request)
    {
        // For local management, we might want to allow creating via API too
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:product_categories,id',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric',
            'stock_quantity' => 'integer',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
