@extends('layouts.admin')

@section('title', 'کاردکس کالا')

@section('content')
<div class="relative">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 mb-2">کاردکس کالا</h2>
            <p class="text-slate-500 font-medium">گزارش ریز گردش و مانده لحظه‌ای کالا</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automation.inventory.reports.index') }}" class="btn-modern btn-modern-light">
                <i class="ti ti-arrow-right"></i>
                بازگشت
            </a>
            @if($inventory)
            <a href="{{ route('automation.inventory.reports.cardex.export', request()->all()) }}" class="btn-modern btn-modern-success">
                <i class="ti ti-file-spreadsheet"></i>
                خروجی اکسل
            </a>
            <button onclick="window.print()" class="btn-modern btn-modern-primary">
                <i class="ti ti-printer"></i>
                چاپ گزارش
            </button>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <x-enhanced-card class="mb-8 print:hidden">
        <form action="{{ route('automation.inventory.reports.cardex') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="form-group-modern md:col-span-2">
                <label class="form-label-modern">انتخاب کالا</label>
                <select name="inventory_id" class="form-control-modern select2" required>
                    <option value="">انتخاب کنید...</option>
                    @foreach($inventories as $item)
                        <option value="{{ $item->id }}" {{ request('inventory_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->sku }}) - موجودی: {{ $item->quantity }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">از تاریخ</label>
                <input type="text" name="start_date" value="{{ request('start_date') }}" class="form-control-modern" placeholder="۱۴۰۴/۰۱/۰۱" dir="ltr">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">تا تاریخ</label>
                <input type="text" name="end_date" value="{{ request('end_date') }}" class="form-control-modern" placeholder="۱۴۰۴/۱۲/۲۹" dir="ltr">
            </div>
            <button type="submit" class="btn-modern btn-modern-primary justify-center h-[52px]">
                <i class="ti ti-filter"></i>
                نمایش کاردکس
            </button>
        </form>
    </x-enhanced-card>

    @if($inventory)
    <!-- Report Table -->
    <x-enhanced-card>
        <div class="mb-6 flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div>
                <h3 class="font-black text-lg text-slate-800">{{ $inventory->name }}</h3>
                <span class="text-xs text-slate-500 font-mono">SKU: {{ $inventory->sku }}</span>
            </div>
            <div class="text-left">
                <span class="text-xs text-slate-500 block">موجودی فعلی</span>
                <span class="font-black text-2xl {{ $inventory->quantity <= $inventory->min_quantity ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ number_format($inventory->quantity) }}
                </span>
            </div>
        </div>

        <div class="table-container">
            <table class="w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">تاریخ</th>
                        <th class="px-4 py-3">نوع تراکنش</th>
                        <th class="px-4 py-3 text-emerald-600">ورده</th>
                        <th class="px-4 py-3 text-rose-600">صادره</th>
                        <th class="px-4 py-3">مانده</th>
                        <th class="px-4 py-3">کاربر</th>
                        <th class="px-4 py-3 w-1/3">توضیحات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $transaction)
                    @php
                        $inQty = $transaction->quantity_change > 0 ? (int) $transaction->quantity_change : 0;
                        $outQty = $transaction->quantity_change < 0 ? abs((int) $transaction->quantity_change) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-slate-600">
                            <x-jalali-date :value="$transaction->created_at" format="Y/m/d H:i" />
                        </td>
                        <td class="px-4 py-3">
                            @if($transaction->transaction_type == 'purchase') <span class="badge badge-success">خرید</span>
                            @elseif($transaction->transaction_type == 'sale') <span class="badge badge-danger">فروش</span>
                            @elseif($transaction->transaction_type == 'use') <span class="badge badge-warning">مصرف</span>
                            @elseif($transaction->transaction_type == 'return') <span class="badge badge-info">برگشت</span>
                            @elseif($transaction->transaction_type == 'warranty_sent') <span class="badge badge-warning">ارسال گارانتی</span>
                            @elseif($transaction->transaction_type == 'warranty_return') <span class="badge badge-success">برگشت گارانتی</span>
                            @else <span class="badge badge-secondary">{{ $transaction->transaction_type }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-emerald-600 font-bold">
                            {{ $inQty > 0 ? '+'.number_format($inQty) : '0' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-rose-600 font-bold">
                            {{ $outQty > 0 ? '-'.number_format($outQty) : '0' }}
                        </td>
                        <td class="px-4 py-3 font-mono font-black text-slate-900 bg-slate-50">
                            {{ number_format((int) $transaction->new_quantity) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-xs">
                            {{ $transaction->user->name ?? 'سیستم' }}
                        </td>
                        <td class="px-4 py-3 text-xs truncate max-w-xs" title="{{ $transaction->notes }}">
                            <x-inventory-transaction-note
                                :note="$transaction->notes"
                                :inventory-url="$transaction->inventory_id ? route('automation.inventory.show', $transaction->inventory_id) : null"
                            />
                            @if($transaction->receiver) <span class="block text-[10px] text-slate-400">تحویل: {{ $transaction->receiver }}</span> @endif
                            @if($transaction->organization) <span class="block text-[10px] text-slate-400">ارگان: {{ $transaction->organization }}</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">تراکنشی یافت نشد</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-enhanced-card>
    @else
    <div class="flex flex-col items-center justify-center py-20 text-slate-300">
        <i class="ti ti-search text-6xl mb-4 opacity-20"></i>
        <p class="font-black uppercase tracking-widest text-sm">لطفاً یک کالا را انتخاب کنید</p>
    </div>
    @endif
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