<div class="p-4 w-80" dir="rtl">
    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-4">
        <h3 class="text-lg font-black text-gray-900">سبد خرید</h3>
        <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded-lg">{{ ($cart->items ?? collect())->count() }} محصول</span>
    </div>

    @if(($cart->items ?? collect())->count() > 0)
        <div class="max-h-96 overflow-y-auto custom-scrollbar mb-4 space-y-4">
            <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex gap-4 items-center group">
                    <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                        <img loading="lazy" src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                            <a href="{{ route('catalog.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                        </h4>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-500">{{ $item->quantity }} عدد</span>
                            <span class="text-sm font-black text-blue-600">{{ number_format($item->price) }} تومان</span>
                        </div>
                    </div>
                    <button type="button" onclick="removeFromMiniCart(this, '{{ $item->product->slug }}')" class="p-1 text-gray-300 hover:text-red-500 transition-colors">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-500 font-medium">مجموع:</span>
                <span class="text-lg font-black text-gray-900">{{ number_format($cart->total) }} تومان</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('cart.index') }}" class="py-3 bg-gray-100 text-gray-900 text-center rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    مشاهده سبد
                </a>
                <a href="{{ route('checkout.index') }}" class="py-3 bg-blue-600 text-white text-center rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">
                    تسویه حساب
                </a>
            </div>
        </div>
    @else
        <div class="py-12 text-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-shopping-cart-off text-3xl"></i>
            </div>
            <p class="text-gray-500 text-sm">سبد خرید شما خالی است</p>
            <a href="{{ route('shop.index') }}" class="text-blue-600 font-bold text-sm mt-4 inline-block hover:underline">مشاهده فروشگاه</a>
        </div>
    @endif
</div>
