@extends('layouts.shop')

@section('title', $product->name . ' - پارس لیان')

@section('shop-content')
<div class="bg-gray-50 min-h-screen py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-reverse space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600 inline-flex items-center text-sm">
                        <i class="ti ti-home ml-2"></i>
                        خانه
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ti ti-chevron-left text-gray-400"></i>
                        <a href="{{ route('catalog.index') }}" class="mr-1 text-sm font-medium text-gray-500 hover:text-blue-600">همه محصولات</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ti ti-chevron-left text-gray-400"></i>
                        <a href="{{ route('catalog.category', $product->category->slug) }}" class="mr-1 text-sm font-medium text-gray-500 hover:text-blue-600">{{ $product->category->name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ti ti-chevron-left text-gray-400"></i>
                        <span class="mr-1 text-sm font-medium text-gray-900">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 p-8 md:p-12">
                <!-- Image Gallery -->
                <div class="space-y-6">
                    <div class="relative w-full pt-[100%] bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden group">
                        <img loading="eager" src="{{ $product->main_image_url }}" alt="{{ $product->name }}" id="mainImage"
                             class="absolute inset-0 z-[1] w-full h-full object-contain p-6 transition-transform duration-500 group-hover:scale-105"
                             onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">

                        <!-- Badges -->
                        <div class="absolute top-6 right-6 z-[2] flex flex-col gap-3">
                            @if($product->is_new)
                            <span class="bg-green-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg shadow-green-200">جدید</span>
                            @endif
                            @if($product->is_featured)
                            <span class="bg-amber-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg shadow-amber-200">پیشنهادی</span>
                            @endif
                        </div>
                    </div>

                    @if(count($product->all_image_urls) > 1)
                    <div class="grid grid-cols-4 md:grid-cols-5 gap-4">
                        @foreach($product->all_image_urls as $imageUrl)
                        <button type="button" onclick="document.getElementById('mainImage').src='{{ $imageUrl }}'" 
                                class="relative w-full pt-[100%] bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-500 transition-all focus:ring-2 focus:ring-blue-200 outline-none overflow-hidden">
                            <img loading="lazy" src="{{ $imageUrl }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-contain p-2">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="flex flex-col">
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-sm font-bold">{{ $product->category->name }}</span>
                            @if($product->brand)
                            <span class="text-gray-400 text-sm font-medium">برند: <span class="text-gray-900 font-bold">{{ $product->brand->name }}</span></span>
                            @endif
                            <span class="text-gray-400 text-sm font-medium mr-auto">شناسه: <span class="text-gray-700">{{ $product->sku }}</span></span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-2 leading-tight">{{ $product->name }}</h1>
                        @if($product->name_en)
                        <p class="text-lg text-gray-400 font-medium mb-4" dir="ltr">{{ $product->name_en }}</p>
                        @endif
                        
                        <div class="flex items-center gap-6 py-4 border-y border-gray-50">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-eye text-blue-500"></i>
                                <span class="text-gray-500 text-sm">{{ number_format($product->view_count) }} بازدید</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="ti ti-package text-green-500"></i>
                                <span class="text-gray-500 text-sm">{{ $product->stock_display }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 bg-gray-50 p-6 rounded-2xl">
                        <div class="flex flex-col gap-2">
                            @if($product->is_on_sale)
                                <div class="flex items-center gap-3">
                                    <span class="text-lg text-gray-400 line-through">{{ number_format($product->price) }} تومان</span>
                                    <span class="bg-red-500 text-white text-sm font-bold px-2 py-0.5 rounded-lg">
                                        {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% تخفیف
                                    </span>
                                </div>
                                <div class="text-4xl font-black text-red-600">
                                    {{ number_format($product->sale_price) }} <span class="text-lg">تومان</span>
                                </div>
                            @else
                                <div class="text-4xl font-black text-gray-900">
                                    {{ number_format($product->price) }} <span class="text-lg">تومان</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-auto space-y-4">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-grow flex flex-col sm:flex-row gap-4">
                                @if($product->is_in_stock)
                                <div class="flex items-center justify-between bg-gray-100 rounded-2xl p-1 border border-gray-200 shadow-inner h-14 w-full sm:w-auto">
                                    <button type="button" class="qty-btn minus w-12 h-full flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors" onclick="updateQty(this, -1)">
                                        <i class="ti ti-minus"></i>
                                    </button>
                                    <input type="number" id="product-quantity" value="1" min="1" max="{{ $product->manage_stock ? $product->stock_quantity : 99 }}" class="qty-input w-16 bg-transparent text-center font-black text-xl text-gray-900 border-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button type="button" class="qty-btn plus w-12 h-full flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors" onclick="updateQty(this, 1)">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                                <button type="button" onclick="addToCart('{{ $product->slug }}', document.getElementById('product-quantity').value, this)" class="flex-grow bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 flex items-center justify-center gap-3 group h-14">
                                    <i class="ti ti-shopping-cart-plus text-2xl group-hover:scale-125 transition"></i>
                                    افزودن به سبد خرید
                                </button>
                                @else
                                <div class="flex-grow bg-red-50 text-red-500 px-8 py-4 rounded-2xl font-black text-lg border border-red-100 text-center h-14 flex items-center justify-center">ناموجود</div>
                                @endif
                            </div>
                            @if($product->external_url)
                            <a href="{{ $product->external_url }}" target="_blank" rel="nofollow" 
                               class="bg-white border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-all flex items-center justify-center gap-3 h-14">
                                <i class="ti ti-external-link text-xl"></i>
                                مشاهده در فروشگاه اصلی
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="border-t border-gray-100">
                <div class="max-w-4xl mx-auto px-8 py-12">
                    <div class="flex gap-8 border-b border-gray-100 mb-8 overflow-x-auto pb-px">
                        <button onclick="switchTab('description')" id="tab-description" 
                                class="text-lg font-bold pb-4 border-b-2 border-blue-600 text-blue-600 whitespace-nowrap transition-all">توضیحات محصول</button>
                        <button onclick="switchTab('specs')" id="tab-specs" 
                                class="text-lg font-bold pb-4 border-b-2 border-transparent text-gray-400 hover:text-gray-600 whitespace-nowrap transition-all">مشخصات فنی</button>
                    </div>

                    <div id="content-description" class="prose prose-blue max-w-none text-gray-600 leading-loose">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                    <div id="content-specs" class="hidden">
                        @if($product->technical_specs && count($product->technical_specs) > 0)
                        <div class="grid gap-4">
                            @foreach($product->technical_specs as $key => $value)
                            <div class="flex flex-col md:flex-row gap-4 py-4 border-b border-gray-50 last:border-0">
                                <div class="md:w-1/3 text-gray-400 font-medium">{{ $key }}</div>
                                <div class="md:w-2/3 text-gray-900 font-bold">{{ is_array($value) ? implode(', ', $value) : $value }}</div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 text-center py-8">مشخصات فنی برای این محصول ثبت نشده است.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-3">
                <i class="ti ti-package text-blue-600"></i>
                محصولات مشابه
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-blue-100 transition-all duration-300">
                    <div class="relative pt-[100%] bg-gray-50 overflow-hidden rounded-xl">
                        <img loading="lazy" src="{{ $related->main_image_url }}" alt="{{ $related->name }}"
                             class="absolute inset-0 z-[1] w-full h-full object-contain p-4 group-hover:scale-110 transition duration-500"
                             onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                        <a href="{{ route('catalog.show', $related->slug) }}" class="absolute inset-0 z-[2]"></a>
                    </div>
                    <div class="p-5">
                        <h3 class="text-gray-900 font-bold mb-2 line-clamp-1 group-hover:text-blue-600 transition">
                            <a href="{{ route('catalog.show', $related->slug) }}">{{ $related->name }}</a>
                        </h3>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-black">{{ number_format($related->current_price) }} <span class="text-[10px]">تومان</span></span>
                            <span class="text-xs text-gray-400">{{ $related->category->name }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function updateQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        const newVal = parseInt(input.value) + delta;
        if (newVal >= parseInt(input.min) && newVal <= parseInt(input.max)) {
            input.value = newVal;
        }
    }

    function switchTab(tab) {
        // Update tab buttons
        const tabs = ['description', 'specs'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            const content = document.getElementById(`content-${t}`);
            
            if (t === tab) {
                btn.classList.remove('border-transparent', 'text-gray-400');
                btn.classList.add('border-blue-600', 'text-blue-600');
                content.classList.remove('hidden');
            } else {
                btn.classList.add('border-transparent', 'text-gray-400');
                btn.classList.remove('border-blue-600', 'text-blue-600');
                content.classList.add('hidden');
            }
        });
    }
</script>

<style>
    .prose p { margin-bottom: 1.5rem; }
</style>
@endsection