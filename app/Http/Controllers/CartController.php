<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\CartItemRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        if ($orderNumber = session('last_completed_order')) {
            return redirect()->route('checkout.success', $orderNumber);
        }

        $cart = Cart::getCurrentCart();
        $cart->load('items.product');

        // Sync prices with current product prices
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product || $product->trashed()) {
                $item->delete();
                continue;
            }
            if ($item->price != $product->current_price) {
                $item->update(['price' => $product->current_price]);
            }
        }

        $cart->calculateTotals();

        return view('shop.cart', compact('cart'));
    }

    public function add(CartItemRequest $request, Product $product)
    {
        $cart = Cart::getCurrentCart();

        if ($cart->addItem($product->id, $request->quantity)) {
            return response()->json([
                'success' => true,
                'message' => 'محصول به سبد خرید اضافه شد.',
                'cart_count' => $cart->item_count,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'متاسفانه محصول موجود نیست یا تعداد درخواست شده بیش از موجودی است.',
        ], 422);
    }

    public function update(CartItemRequest $request, Product $product)
    {
        $cart = Cart::getCurrentCart();

        if ($request->quantity > 0) {
            $result = $cart->updateItemQuantity($product->id, $request->quantity);
        } else {
            $result = $cart->removeItem($product->id);
        }

        if ($result) {
            $cart->refresh();

            return response()->json([
                'success' => true,
                'cart_count' => $cart->item_count,
                'subtotal' => number_format($cart->subtotal),
                'total' => number_format($cart->total),
                'item_subtotal' => number_format($cart->items()->where('product_id', $product->id)->first()?->subtotal ?? 0),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'خطا در بروزرسانی سبد خرید.',
        ], 422);
    }

    public function remove(Request $request, Product $product)
    {
        $cart = Cart::getCurrentCart();
        $cart->removeItem($product->id);

        if ($request->ajax() || $request->wantsJson()) {
            $cart->refresh();
            return response()->json([
                'success' => true,
                'message' => 'محصول از سبد خرید حذف شد.',
                'cart_count' => $cart->item_count,
                'subtotal' => number_format($cart->subtotal),
                'total' => number_format($cart->total),
            ]);
        }

        return redirect()->back()->with('success', 'محصول از سبد خرید حذف شد.');
    }

    public function clear()
    {
        $cart = Cart::getCurrentCart();
        $cart->items()->delete();
        $cart->calculateTotals();

        return redirect()->back()->with('success', 'سبد خرید پاک شد.');
    }

    public function count()
    {
        $cart = Cart::getCurrentCart();

        return response()->json([
            'count' => $cart->item_count,
        ]);
    }

    public function miniCart()
    {
        $cart = Cart::getCurrentCart();
        return view('shop.partials.mini-cart', compact('cart'));
    }
}
