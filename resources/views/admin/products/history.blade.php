@extends('layouts.admin')

@section('title', 'تاریخچه محصول - ' . $product->name)

@section('content')
<div class="p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800">تاریخچه محصول</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $product->name }} — فروش، تعمیر، انبار و ویرایش</p>
        </div>
        <a href="{{ route('admin.products.edit', $product) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all">
            <i class="ti ti-arrow-right text-xl"></i>
            بازگشت به ویرایش
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm mb-8">
        <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i class="ti ti-timeline-event text-blue-600"></i>
            رویدادها (فروش آنلاین، POS، تعمیر، انبار)
        </h2>
        @include('admin.products.partials.activity-timeline', ['activities' => $activities ?? collect()])
    </div>

    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
        <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i class="ti ti-versions text-slate-500"></i>
            نسخه‌های ذخیره‌شده (ویرایش مشخصات)
        </h2>
        <div class="space-y-6">
            @forelse($versions as $version)
            <div class="rounded-2xl p-6 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full bg-slate-300"></div>
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-xs font-black">{{ $version->created_at->format('Y-m-d H:i') }}</span>
                        <span class="text-slate-400 text-xs font-bold mr-2">{{ $version->user->name ?? 'سیستم' }}</span>
                        <h3 class="text-base font-black text-slate-800 mt-2">{{ $version->change_reason ?? 'بدون توضیح' }}</h3>
                    </div>
                    <button type="button" onclick="toggleDetails('version-{{ $version->id }}')" class="text-blue-600 font-bold text-sm">جزئیات</button>
                </div>
                <div id="version-{{ $version->id }}" class="hidden pt-4 border-t border-slate-50 text-sm text-slate-600">
                    موجودی: {{ $version->data['stock_quantity'] ?? '—' }} — قیمت: {{ number_format($version->data['price'] ?? 0) }}
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-6">نسخه‌ای ثبت نشده است.</p>
            @endforelse
        </div>
    </div>
</div>

@section('scripts')
<script>
    function toggleDetails(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection
@endsection
