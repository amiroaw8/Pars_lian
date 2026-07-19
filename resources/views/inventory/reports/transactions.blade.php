@extends('layouts.admin')

@section('title', 'لیست تراکنش‌ها')

@section('content')
<div class="relative">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 mb-2">لیست تراکنش‌ها</h2>
            <p class="text-slate-500 font-medium">گزارش کلیه حواله‌ها و رسیدهای انبار</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automation.inventory.reports.index') }}" class="btn-modern btn-modern-light">
                <i class="ti ti-arrow-right"></i>
                بازگشت
            </a>
            <a href="{{ route('automation.inventory.reports.transactions.export', request()->all()) }}" class="btn-modern btn-modern-success">
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
        <form action="{{ route('automation.inventory.reports.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="form-group-modern">
                <label class="form-label-modern">نوع تراکنش</label>
                <select name="transaction_type" class="form-control-modern select2">
                    <option value="">همه موارد</option>
                    <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>خرید</option>
                    <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>فروش</option>
                    <option value="use" {{ request('transaction_type') == 'use' ? 'selected' : '' }}>مصرف</option>
                    <option value="return" {{ request('transaction_type') == 'return' ? 'selected' : '' }}>برگشت</option>
                    <option value="warranty_sent" {{ request('transaction_type') == 'warranty_sent' ? 'selected' : '' }}>ارسال گارانتی</option>
                    <option value="warranty_return" {{ request('transaction_type') == 'warranty_return' ? 'selected' : '' }}>برگشت گارانتی</option>
                </select>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">کاربر ثبت کننده</label>
                <select name="user_id" class="form-control-modern select2">
                    <option value="">همه کاربران</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
            <button type="submit" class="btn-modern btn-modern-primary justify-center h-[52px] md:col-span-4 w-full">
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
                        <th class="px-4 py-3">تاریخ</th>
                        <th class="px-4 py-3">کالا</th>
                        <th class="px-4 py-3">نوع</th>
                        <th class="px-4 py-3 text-emerald-600">تعداد</th>
                        <th class="px-4 py-3">موجودی جدید</th>
                        <th class="px-4 py-3">کاربر</th>
                        <th class="px-4 py-3 w-1/4">جزئیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-slate-600">
                            <x-jalali-date :value="$transaction->created_at" format="Y/m/d H:i" />
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-700">
                            {{ $transaction->inventory->name }}
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
                        <td class="px-4 py-3 font-mono font-bold {{ $transaction->quantity_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ $transaction->quantity_change }}
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
        <div class="mt-4">
            {{ $transactions->links() }}
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