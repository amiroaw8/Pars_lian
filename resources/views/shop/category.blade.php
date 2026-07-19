@extends('layouts.shop')

@section('title', $category->name . ' | فروشگاه پارس لیان')

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
            @foreach($categoryPath as $path)
            <li>
                <div class="flex items-center">
                    <i class="ti ti-chevron-left text-gray-400 mx-2 text-xs"></i>
                    @if($loop->last)
                        <span class="text-sm font-bold text-blue-600">{{ $path->name }}</span>
                    @else
                        <a href="{{ route('shop.category', $path) }}" class="text-sm font-medium text-gray-500 hover:text-blue-600">
                            {{ $path->name }}
                        </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="relative bg-gradient-to-br from-slate-900 to-blue-900 rounded-[3rem] p-12 mb-16 overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">{{ $category->name }}</h1>
            <p class="text-blue-200 text-lg max-w-2xl leading-relaxed">{{ $category->description ?? 'بهترین قطعات و تجهیزات ' . $category->name . ' را در پارس لیان بیابید.' }}</p>
        </div>
        
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl -ml-24 -mb-24"></div>
    </div>

    @if($subcategories->count() > 0)
    <!-- Subcategories -->
    <section class="mb-16">
        <h2 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
            <i class="ti ti-category text-blue-600"></i>
            زیرمجموعه‌های {{ $category->name }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($subcategories as $sub)
            <a href="{{ route('shop.category', $sub) }}" class="group bg-white rounded-3xl p-6 border border-slate-100 hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all text-center">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="ti ti-folder text-xl"></i>
                </div>
                <span class="font-bold text-slate-700 group-hover:text-blue-600 transition-colors">{{ $sub->name }}</span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Products Grid -->
    <div class="flex flex-col gap-8">
        <!-- Toolbar -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                    <i class="ti ti-layout-grid text-xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900">محصولات</h3>
                    <p class="text-slate-500 text-xs">نمایش <span class="font-bold text-blue-600">{{ $products->total() }}</span> کالا</p>
                </div>
            </div>

            <div class="flex items-center gap-4 w-full lg:w-auto">
                <form method="GET" class="flex items-center gap-3 w-full lg:w-auto">
                    <span class="text-sm font-bold text-slate-400 whitespace-nowrap ml-2">مرتب‌سازی:</span>
                    <select name="sort" onchange="this.form.submit()"
                            class="w-full lg:w-48 text-sm font-bold bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-blue-500/10 px-5 py-2.5 outline-none transition-all">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الفبایی</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>ارزان‌ترین</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>گران‌ترین</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>جدیدترین‌ها</option>
                    </select>
                </form>
            </div>
        </div>

        @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
            <div class="group bg-white rounded-[2rem] border border-slate-100 p-4 hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-500">
                <div class="relative overflow-hidden rounded-2xl pt-[100%] bg-slate-50 mb-6">
                    <img loading="lazy" src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
                         class="absolute inset-0 z-[1] w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-700"
                         onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">

                    @if($product->is_on_sale)
                    <div class="absolute top-4 right-4 bg-rose-500 text-white px-3 py-1 rounded-lg text-[10px] font-black z-[3]">
                        {{ round((1 - $product->sale_price / $product->price) * 100) }}% تخفیف
                    </div>
                    @endif

                    <div class="absolute inset-0 z-[2] bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center gap-3 pointer-events-none group-hover:pointer-events-auto">
                        <button type="button" 
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all shadow-xl translate-y-4 group-hover:translate-y-0 transition-all btn-add-to-cart"
                                data-product-slug="{{ $product->slug }}">
                            <i class="ti ti-shopping-cart text-xl"></i>
                        </button>
                        <a href="{{ route('shop.show', $product) }}" class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all shadow-xl translate-y-4 group-hover:translate-y-0 transition-all delay-75">
                            <i class="ti ti-eye text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div class="px-2 pb-2">
                    <h3 class="font-bold text-slate-800 mb-2 line-clamp-1 group-hover:text-blue-600 transition-colors">
                        <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
                    </h3>
                    <div class="flex justify-between items-center mt-4">
                        <div class="flex flex-col">
                            @if($product->is_on_sale)
                                <span class="text-[10px] text-slate-400 line-through">{{ number_format($product->price) }}</span>
                                <span class="text-lg font-black text-blue-600">{{ number_format($product->sale_price) }} <small class="text-[10px] font-bold text-slate-400">تومان</small></span>
                            @else
                                <span class="text-lg font-black text-slate-900">{{ number_format($product->price) }} <small class="text-[10px] font-bold text-slate-400">تومان</small></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-16 flex justify-center">
            {{ $products->links() }}
        </div>
        @else
        <div class="bg-slate-50 rounded-[3rem] py-24 text-center border border-dashed border-slate-200">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                <i class="ti ti-package-off text-5xl text-slate-300"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-3">محصولی در این دسته یافت نشد</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">در حال حاضر هیچ کالایی در این دسته‌بندی موجود نیست. می‌توانید از سایر بخش‌ها دیدن کنید.</p>
            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                بازگشت به فروشگاه
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
