@extends('layouts.shop')

@section('title', 'سبد خرید - پارس لیان')

@section('shop-content')
<div class="py-12 bg-gray-50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-reverse space-x-2">
                <li><a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">فروشگاه</a></li>
                <li class="flex items-center">
                    <i class="ti ti-chevron-left text-gray-400 mx-2"></i>
                    <span class="text-gray-900">سبد خرید</span>
                </li>
            </ol>
        </nav>

        <h1 class="text-4xl font-black text-gray-900 mb-10 tracking-tight">سبد خرید شما</h1>

        @if($cart->items->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Cart Items List -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="divide-y divide-gray-100">
                            @foreach($cart->items as $item)
                                <div class="p-8 flex flex-col sm:flex-row items-center gap-8 group transition-all hover:bg-gray-50/50 cart-item-row">
                                    <!-- Product Image -->
                                    <div class="w-32 h-32 bg-gray-50 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        <img loading="lazy" src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 text-center sm:text-right">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                            <a href="{{ route('catalog.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                        </h3>
                                        <p class="text-gray-500 text-sm mb-4">کد محصول: {{ $item->product->sku ?? 'N/A' }}</p>
                                        
                                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-6">
                                            <div class="text-lg font-black text-blue-600">{{ number_format($item->price) }} تومان</div>
                                            
                                            <!-- Quantity Selector -->
                                            <div class="flex items-center bg-gray-100 rounded-xl p-1 border border-gray-200 quantity-selector">
                                                <button type="button" 
                                                    onclick="changeQuantity(this, -1)" 
                                                    data-slug="{{ $item->product->slug }}"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-white rounded-lg transition-all">
                                                    <i class="ti ti-minus pointer-events-none"></i>
                                                </button>
                                                <span class="w-10 text-center font-bold text-gray-900 quantity-display">{{ $item->quantity }}</span>
                                                <button type="button" 
                                                    onclick="changeQuantity(this, 1)" 
                                                    data-slug="{{ $item->product->slug }}"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-white rounded-lg transition-all">
                                                    <i class="ti ti-plus pointer-events-none"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item Subtotal & Remove -->
                                    <div class="text-center sm:text-left space-y-4" id="item-row-{{ $item->product->slug }}">
                                        <div class="text-xl font-black text-gray-900"><span id="subtotal-{{ $item->product->slug }}">{{ number_format($item->subtotal) }}</span> تومان</div>
                                        <button type="button" onclick="removeItem(this, '{{ $item->product->slug }}')" class="text-red-500 hover:text-red-600 font-bold text-sm flex items-center gap-1 mx-auto sm:mr-auto sm:ml-0 transition-colors">
                                            <i class="ti ti-trash"></i>
                                            حذف محصول
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cart Actions -->
                    <div class="flex justify-between items-center px-4">
                        <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-bold transition-colors">
                            <i class="ti ti-arrow-right"></i>
                            ادامه خرید
                        </a>
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('آیا از پاکسازی سبد خرید اطمینان دارید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 font-medium text-sm transition-colors">
                                پاکسازی سبد خرید
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 p-8 sticky top-28">
                        <h2 class="text-2xl font-black text-gray-900 mb-8">خلاصه سفارش</h2>
                        
                        <div class="space-y-6 mb-8">
                            <div class="flex justify-between text-gray-600">
                                <span>جمع جزء</span>
                                <span class="font-bold text-gray-900"><span id="cart-subtotal">{{ number_format($cart->subtotal) }}</span> تومان</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>هزینه ارسال</span>
                                <span class="text-amber-600 font-bold">نوع ارسال هنوز مشخص نشده</span>
                            </div>
                            @if($cart->discount_amount > 0)
                                <div class="flex justify-between text-red-500">
                                    <span>تخفیف</span>
                                    <span class="font-bold">-{{ number_format($cart->discount_amount) }} تومان</span>
                                </div>
                            @endif
                            <div class="pt-6 border-t border-gray-100 flex justify-between items-end">
                                <span class="text-gray-900 font-bold">مبلغ قابل پرداخت</span>
                                <div class="text-right">
                                    <div class="text-3xl font-black text-blue-600" id="cart-total">{{ number_format($cart->total) }}</div>
                                    <div class="text-xs text-gray-400 mt-1">تومان</div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="w-full py-5 bg-gray-900 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-blue-600 hover:scale-[1.02] transition-all shadow-2xl shadow-gray-200">
                            تکمیل سفارش و پرداخت
                            <i class="ti ti-credit-card"></i>
                        </a>

                        <div class="mt-8 flex items-center justify-center gap-4 text-gray-400">
                            <i class="ti ti-shield-check text-2xl"></i>
                            <span class="text-xs font-medium">پرداخت امن و تضمین اصالت کالا</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart State -->
            <div class="bg-white rounded-[3rem] p-20 text-center border border-dashed border-gray-200 shadow-sm">
                <div class="w-32 h-32 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-10">
                    <i class="ti ti-shopping-cart-x text-6xl"></i>
                </div>
                <h2 class="text-3xl font-black text-gray-900 mb-4">سبد خرید شما خالی است</h2>
                <p class="text-gray-500 mb-10 max-w-md mx-auto leading-relaxed">به نظر می‌رسد هنوز هیچ محصولی به سبد خرید خود اضافه نکرده‌اید. از کاتالوگ محصولات ما دیدن کنید.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-700 hover:scale-105 transition-all shadow-xl shadow-blue-200">
                    مشاهده محصولات
                    <i class="ti ti-arrow-left"></i>
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function changeQuantity(btn, delta) {
        const container = btn.closest('.quantity-selector');
        if (!container) return;
        
        const display = container.querySelector('.quantity-display');
        if (!display) return;
        
        const currentQty = parseInt(display.innerText);
        const newQty = currentQty + delta;
        const slug = btn.dataset.slug;
        
        if (newQty < 1) return;
        
        updateQuantity(btn, slug, newQty);
    }

    function updateQuantity(btn, productSlug, quantity) {
        if (quantity < 1) return;
        
        const container = btn.closest('.quantity-selector');
        if (!container) return;
        
        const quantityDisplay = container.querySelector('.quantity-display');
        const minusBtn = container.querySelector('button:first-child');
        const plusBtn = container.querySelector('button:last-child');
        
        // Disable buttons during request
        minusBtn.disabled = true;
        plusBtn.disabled = true;
        btn.classList.add('opacity-50');
        
        fetch(`/cart/update/${productSlug}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update item subtotal
                const subtotalEl = document.getElementById(`subtotal-${productSlug}`);
                if (subtotalEl) subtotalEl.innerText = data.item_subtotal;
                
                // Update cart totals
                const cartSubtotalEl = document.getElementById('cart-subtotal');
                const cartTotalEl = document.getElementById('cart-total');
                if (cartSubtotalEl) cartSubtotalEl.innerText = data.subtotal;
                if (cartTotalEl) cartTotalEl.innerText = data.total;
                
                // Update quantity display
                quantityDisplay.innerText = quantity;
                
                // Update cart count in header
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = data.cart_count);

                // Update mini cart if exists
                if (typeof updateMiniCart === 'function') {
                    updateMiniCart();
                }

                if (typeof showToast === 'function') {
                    showToast('سبد خرید بروزرسانی شد', 'success');
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message || 'خطا در بروزرسانی سبد خرید', 'error');
                } else {
                    alert(data.message || 'خطا در بروزرسانی سبد خرید');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showToast === 'function') {
                showToast('خطایی در سیستم رخ داد', 'error');
            } else {
                alert('خطایی در سیستم رخ داد.');
            }
        })
        .finally(() => {
            minusBtn.disabled = false;
            plusBtn.disabled = false;
            btn.classList.remove('opacity-50');
        });
    }
    function removeItem(btn, productSlug) {
        if (!confirm('آیا از حذف این محصول اطمینان دارید؟')) return;

        btn.disabled = true;
        btn.classList.add('opacity-50');

        fetch(`/cart/remove/${productSlug}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the item element
                const row = btn.closest('.cart-item-row');
                if (!row) {
                    location.reload();
                    return;
                }
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();

                    // Check if cart is empty now
                    const remainingItems = document.querySelectorAll('.cart-item-row');
                    if (remainingItems.length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 300);

                // Update cart totals
                const cartSubtotalEl = document.getElementById('cart-subtotal');
                const cartTotalEl = document.getElementById('cart-total');
                if (cartSubtotalEl) cartSubtotalEl.innerText = data.subtotal;
                if (cartTotalEl) cartTotalEl.innerText = data.total;
                
                // Update cart count in header
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = data.cart_count);

                // Update mini cart if exists
                if (typeof updateMiniCart === 'function') {
                    updateMiniCart();
                }

                if (typeof showToast === 'function') {
                    showToast('محصول از سبد خرید حذف شد', 'success');
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message || 'خطا در حذف محصول', 'error');
                } else {
                    alert(data.message || 'خطا در حذف محصول');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showToast === 'function') {
                showToast('خطایی در سیستم رخ داد', 'error');
            } else {
                alert('خطایی در سیستم رخ داد.');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        });
    }
</script>
@endpush
@endsection
