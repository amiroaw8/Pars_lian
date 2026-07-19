@extends('layouts.admin')

@section('title', 'مدیریت محصولات - پارس لیان')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800">مدیریت محصولات</h1>
            <p class="text-slate-500 text-sm mt-1">مشاهده و مدیریت تمام محصولات فروشگاه</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.export') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all">
                <i class="ti ti-download text-xl"></i>
                خروجی CSV
            </a>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-600/20">
                <i class="ti ti-plus text-xl"></i>
                افزودن محصول جدید
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">جستجو</label>
                <div class="relative">
                    <i class="ti ti-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="نام، SKU یا نام انگلیسی..." class="w-full bg-slate-50 border-none rounded-xl py-3 pr-11 pl-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">دسته‌بندی</label>
                <select name="category_id" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">همه دسته‌بندی‌ها</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">وضعیت</label>
                <select name="status" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>حذف شده (سطل زباله)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl font-bold text-sm transition-all">اعمال فیلتر</button>
                <a href="{{ route('admin.products.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 p-3 rounded-xl transition-all">
                    <i class="ti ti-refresh text-xl"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">تصویر و نام</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">SKU</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">قیمت</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">موجودی</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">وضعیت</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 p-2 border border-slate-100 overflow-hidden shrink-0">
                                    <img loading="lazy" src="{{ $product->main_image_url }}" alt="" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $product->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ $product->category->name ?? 'بدون دسته‌بندی' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 font-mono text-sm text-slate-600 uppercase">{{ $product->sku }}</td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                @if($product->is_on_sale)
                                    <span class="text-xs text-slate-400 line-through">{{ number_format($product->price) }}</span>
                                    <span class="font-black text-blue-600">{{ number_format($product->sale_price) }} <small class="text-[10px] font-bold text-slate-400">تومان</small></span>
                                @else
                                    <span class="font-black text-slate-800">{{ number_format($product->price) }} <small class="text-[10px] font-bold text-slate-400">تومان</small></span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1.5 rounded-xl text-xs font-black {{ $product->stock_quantity > 5 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                {{ $product->stock_quantity }} عدد
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            @if($product->trashed())
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-slate-100 text-slate-500">حذف شده</span>
                            @else
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black {{ $product->is_active ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $product->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                @if($product->trashed())
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all" title="بازیابی">
                                            <i class="ti ti-rotate-clockwise text-xl"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" onsubmit="return confirm('آیا از حذف دائمی این محصول اطمینان دارید؟ این عمل غیرقابل بازگشت است.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all" title="حذف دائمی">
                                            <i class="ti ti-trash-x text-xl"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.products.edit', $product) }}" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all" title="ویرایش">
                                        <i class="ti ti-edit text-xl"></i>
                                    </a>
                                    @if(!$product->trashed() && $product->stock_quantity > 0)
                                    <form action="{{ route('admin.products.mark-out-of-stock', $product) }}" method="POST" onsubmit="return confirm('موجودی این محصول صفر شود و در فروشگاه ناموجود نمایش داده شود؟')">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all" title="اتمام موجودی">
                                            <i class="ti ti-box-off text-xl"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.products.history', $product) }}" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-800 hover:text-white transition-all" title="تاریخچه">
                                        <i class="ti ti-history text-xl"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all" title="حذف">
                                            <i class="ti ti-trash text-xl"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="ti ti-package-off text-6xl text-slate-200 mb-4"></i>
                                <h3 class="text-lg font-bold text-slate-400">هیچ محصولی یافت نشد</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-6 py-5 bg-slate-50 border-t border-slate-100">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
