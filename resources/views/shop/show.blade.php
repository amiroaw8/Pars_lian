@extends('layouts.shop')

@section('title', $product->name . ' | فروشگاه پارس لیان')

@section('shop-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 overflow-x-auto no-scrollbar py-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 flex items-center">
                    <i class="ti ti-home ml-2"></i>
                    فروشگاه
                </a>
            </li>
            @foreach($categories as $category)
            <li>
                <div class="flex items-center">
                    <i class="ti ti-chevron-left text-gray-400 mx-2 text-xs"></i>
                    <a href="{{ route('shop.category', $category) }}" class="text-sm font-medium text-gray-500 hover:text-blue-600">
                        {{ $category->name }}
                    </a>
                </div>
            </li>
            @endforeach
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="ti ti-chevron-left text-gray-400 mx-2 text-xs"></i>
                    <span class="text-sm font-bold text-blue-600 truncate max-w-[200px]">
                        {{ $product->name }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 p-8 lg:p-12">
            <!-- Product Images -->
            <div class="space-y-6">
                <div class="relative w-full pt-[100%] bg-slate-50 rounded-[2.5rem] overflow-hidden border border-slate-100 group">
                    <img loading="eager" src="{{ $product->main_image_url }}"
                         alt="{{ $product->name }}"
                         class="absolute inset-0 z-[1] w-full h-full object-contain p-6 group-hover:scale-105 transition-transform duration-700"
                         onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                    
                    @if($product->is_on_sale)
                    <div class="absolute top-6 right-6 bg-rose-500 text-white px-4 py-2 rounded-2xl text-xs font-black shadow-lg shadow-rose-500/30">
                        {{ round((1 - $product->sale_price / $product->price) * 100) }}% تخفیف ویژه
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col">
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 uppercase tracking-wider mb-4">
                        {{ $product->category->name ?? 'سخت‌افزار' }}
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-4">
                        {{ $product->name }}
                    </h1>
                    <div class="flex items-center gap-4 text-sm text-slate-500">
                        <div class="flex items-center">
                            <i class="ti ti-hash ml-1 text-blue-500"></i>
                            کد کالا: <span class="font-bold mr-1">{{ $product->sku ?? 'PL-' . $product->id }}</span>
                        </div>
                        <div class="h-4 w-px bg-slate-200"></div>
                        <div class="flex items-center">
                            <i class="ti ti-eye ml-1 text-purple-500"></i>
                            {{ number_format($product->view_count) }} بازدید
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-[2rem] p-6 mb-8 border border-slate-100">
                    <div class="flex flex-col gap-1 mb-6">
                        @if($product->is_on_sale)
                            <span class="text-sm text-slate-400 line-through">{{ number_format($product->price) }} تومان</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-blue-600 tracking-tight">{{ number_format($product->sale_price) }}</span>
                                <span class="text-lg font-bold text-slate-400">تومان</span>
                            </div>
                        @else
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-slate-900 tracking-tight">{{ number_format($product->price) }}</span>
                                <span class="text-lg font-bold text-slate-400">تومان</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center bg-white rounded-2xl border border-slate-200 p-1">
                            <button type="button" onclick="decrementQty()" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors">
                                <i class="ti ti-minus"></i>
                            </button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" 
                                   class="w-12 text-center font-bold bg-transparent border-none focus:ring-0">
                            <button type="button" onclick="incrementQty()" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                        
                        <button type="button" 
                                id="btn-add-main"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-3 btn-add-to-cart"
                                data-product-slug="{{ $product->slug }}">
                            <i class="ti ti-shopping-cart text-xl"></i>
                            افزودن به سبد خرید
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-4 p-4 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-shield-check text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">ضمانت اصالت و سلامت کالا</h4>
                            <p class="text-xs text-slate-500">تمامی محصولات پارس لیان با تضمین کیفیت ارائه می‌شوند.</p>
                        </div>
                    </div>
                    
                    <div class="prose prose-slate max-w-none">
                        <h3 class="text-lg font-black text-slate-900 mb-4">توضیحات محصول</h3>
                        <div class="text-slate-600 leading-relaxed">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="mt-24">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-2xl font-black text-slate-900">محصولات مشابه</h2>
            <a href="{{ route('shop.category', $product->category) }}" class="text-blue-600 font-bold hover:underline flex items-center gap-2">
                مشاهده همه
                <i class="ti ti-arrow-left"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($relatedProducts as $related)
            <div class="group bg-white rounded-[2rem] border border-slate-100 p-4 hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-500">
                <div class="relative overflow-hidden rounded-2xl pt-[100%] bg-slate-50 mb-6">
                    <img loading="lazy" src="{{ $related->main_image_url }}" alt="{{ $related->name }}"
                         class="absolute inset-0 z-[1] w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-700"
                         onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                    <div class="absolute inset-0 z-[2] bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center gap-3 pointer-events-none group-hover:pointer-events-auto">
                        <button type="button" 
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all shadow-xl translate-y-4 group-hover:translate-y-0 transition-all btn-add-to-cart"
                                data-product-slug="{{ $related->slug }}">
                            <i class="ti ti-shopping-cart text-xl"></i>
                        </button>
                        <a href="{{ route('shop.show', $related) }}" class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all shadow-xl translate-y-4 group-hover:translate-y-0 transition-all delay-75">
                            <i class="ti ti-eye text-xl"></i>
                        </a>
                    </div>
                </div>
                <div class="px-2">
                    <h3 class="font-bold text-slate-800 mb-2 line-clamp-1 group-hover:text-blue-600 transition-colors">
                        <a href="{{ route('shop.show', $related) }}">{{ $related->name }}</a>
                    </h3>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-black text-slate-900">{{ number_format($related->price) }} <small class="text-[10px] font-bold text-slate-400">تومان</small></span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<script>
    function incrementQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.getAttribute('max')) || 99;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const btnAddMain = document.getElementById('btn-add-main');
        if (btnAddMain) {
            btnAddMain.addEventListener('click', function() {
                const qty = document.getElementById('quantity').value;
                const productSlug = this.dataset.productSlug;
                if (typeof addToCart === 'function') {
                    addToCart(productSlug, qty, this);
                }
            });
        }
    });
</script>
@endsection
