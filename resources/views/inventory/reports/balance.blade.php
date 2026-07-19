@extends('layouts.admin')

@section('title', 'ترازنامه انبار')

@section('content')
<div class="relative">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 mb-2">ترازنامه انبار</h2>
            <p class="text-slate-500 font-medium">گزارش خلاصه وضعیت موجودی کالاها در بازه زمانی</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automation.inventory.reports.index') }}" class="btn-modern btn-modern-light">
                <i class="ti ti-arrow-right"></i>
                بازگشت
            </a>
            <a href="{{ route('automation.inventory.reports.balance.export', request()->all()) }}" class="btn-modern btn-modern-success">
                <i class="ti ti-file-spreadsheet"></i>
                خروجی اکسل
            </a>
            <button onclick="window.print()" class="btn-modern btn-modern-primary">
                <i class="ti ti-printer"></i>
                چاپ گزارش
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <x-enhanced-card class="mb-8 print:hidden">
        <form action="{{ route('automation.inventory.reports.balance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="form-group-modern">
                <label class="form-label-modern">از تاریخ</label>
                <input type="text" name="start_date" value="{{ request('start_date') }}" class="form-control-modern" placeholder="۱۴۰۴/۰۱/۰۱" dir="ltr">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">تا تاریخ</label>
                <input type="text" name="end_date" value="{{ request('end_date') }}" class="form-control-modern" placeholder="۱۴۰۴/۱۲/۲۹" dir="ltr">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">دسته‌بندی</label>
                <select name="category" class="form-control-modern">
                    <option value="">همه موارد</option>
                    <option value="device" {{ request('category') == 'device' ? 'selected' : '' }}>دستگاه</option>
                    <option value="part" {{ request('category') == 'part' ? 'selected' : '' }}>قطعه</option>
                    <option value="tool" {{ request('category') == 'tool' ? 'selected' : '' }}>ابزار</option>
                </select>
            </div>
            <button type="submit" class="btn-modern btn-modern-primary justify-center h-[52px]">
                <i class="ti ti-filter"></i>
                اعمال فیلتر
            </button>
        </form>
    </x-enhanced-card>

    <!-- Report Table -->
    <x-enhanced-card>
        <div class="table-container">
            <table class="w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">کالا</th>
                        <th class="px-4 py-3">موجودی ابتدای دوره</th>
                        <th class="px-4 py-3 text-emerald-600">وارده</th>
                        <th class="px-4 py-3 text-rose-600">صادره</th>
                        <th class="px-4 py-3">موجودی پایان دوره</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-slate-700">
                            <div class="flex flex-col">
                                <span>{{ $item->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->sku }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono">{{ number_format($item->opening_stock) }}</td>
                        <td class="px-4 py-3 font-mono text-emerald-600 font-bold">+{{ number_format($item->total_in) }}</td>
                        <td class="px-4 py-3 font-mono text-rose-600 font-bold">-{{ number_format($item->total_out) }}</td>
                        <td class="px-4 py-3 font-mono font-black text-slate-900 bg-slate-50">{{ number_format($item->closing_stock) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">اطلاعاتی یافت نشد</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 font-black text-slate-900 border-t-2 border-slate-200">
                    <tr>
                        <td class="px-4 py-3">جمع کل</td>
                        <td class="px-4 py-3 font-mono">{{ number_format($items->sum('opening_stock')) }}</td>
                        <td class="px-4 py-3 font-mono text-emerald-600">{{ number_format($items->sum('total_in')) }}</td>
                        <td class="px-4 py-3 font-mono text-rose-600">{{ number_format($items->sum('total_out')) }}</td>
                        <td class="px-4 py-3 font-mono">{{ number_format($items->sum('closing_stock')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-enhanced-card>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .min-h-\[calc\(100vh-12rem\)\] * {
            visibility: visible;
        }
        .min-h-\[calc\(100vh-12rem\)\] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none !important;
        }
        .btn-modern {
            display: none !important;
        }
    }
</style>
@endsection