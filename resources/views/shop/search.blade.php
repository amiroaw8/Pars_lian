@extends('layouts.shop')

@section('title', 'جستجوی محصولات - پارس لیان')
@section('meta_description', 'نتایج جستجوی کالا و قطعات کامپیوتر در فروشگاه اینترنتی پارس لیان.')
@section('canonical', request()->fullUrl())

@section('shop-content')
<div class="py-12 bg-gray-50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-900 mb-2">نتایج جستجو برای: <span class="text-blue-600">"{{ $query ?? '' }}"</span></h1>
            <p class="text-sm text-gray-500">تعداد کالا‌های یافت شده: {{ isset($products) ? $products->total() : 0 }}</p>
        </div>

        @if(isset($products) && $products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <a href="{{ route('catalog.show', $product->slug ?? $product->id) }}" class="block">
                            <div class="aspect-square bg-gray-100 rounded-xl mb-4 overflow-hidden flex items-center justify-center">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2">
                                @else
                                    <i class="ti ti-device-laptop text-4xl text-gray-300"></i>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2">{{ $product->name }}</h3>
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-blue-600 font-black text-sm">{{ number_format($product->price) }} تومان</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100">
                <i class="ti ti-search-off text-6xl text-gray-300 mb-4 block"></i>
                <h3 class="text-lg font-bold text-gray-800 mb-2">محصولی با این عبارت یافت نشد</h3>
                <p class="text-gray-500 text-sm mb-6">لطفاً املای کلمه را بررسی کرده یا عبارت دیگری را جستجو کنید.</p>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">
                    <i class="ti ti-grid-dots"></i>
                    مشاهده همه محصولات
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
