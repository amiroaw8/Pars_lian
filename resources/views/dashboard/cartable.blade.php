@extends('layouts.admin')

@section('title', $title . ' - پارس لیان')

@section('content')
@php
    $showTechnician = in_array($type, ['repair', 'bank', 'accounting'], true);
    $showCost = in_array($type, ['accounting', 'bank'], true);
    $colCount = 5 + ($showTechnician ? 1 : 0) + ($showCost ? 1 : 0);
@endphp

<div class="relative min-h-screen">
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] rounded-full bg-gradient-to-br from-primary-500/10 to-indigo-500/10 blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] -left-[10%] w-[35%] h-[35%] rounded-full bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 blur-[100px] animate-blob animation-delay-2000"></div>
    </div>

    <x-page-header
        :title="$title"
        subtitle="مدیریت سفارشات و فرآیندهای مربوط به {{ $title }}"
        :badge="$title"
        badgeIcon="ti-list-check"
        headerIcon="ti-clipboard-list"
        class="mb-8"
    />

    <div class="animate-fade-in space-y-8">
        @if($type === 'bank')
        <div class="filter-card">
            <form action="{{ route('automation.dashboard.bank') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="bank-search" class="form-label">
                        <i class="ti ti-search text-primary-500"></i>
                        جستجو
                    </label>
                    <input
                        type="text"
                        id="bank-search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="جستجو در کد رهگیری، نام مشتری، مدل دستگاه..."
                        class="form-control"
                    >
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-modern btn-modern-primary w-full sm:w-auto justify-center">
                        <i class="ti ti-search"></i>
                        جستجو
                    </button>
                </div>
            </form>
        </div>
        @endif

        <x-enhanced-table striped hover responsive title="لیست سفارشات" icon="ti ti-list">
            <x-slot name="headerAction">
                <span class="text-sm font-bold text-slate-500">تعداد: {{ $orders->total() }}</span>
            </x-slot>

            <x-slot name="headers">
                <th>کد رهگیری</th>
                <th>مشتری</th>
                <th>دستگاه</th>
                <th>وضعیت</th>
                @if($showTechnician)
                    <th>تکنسین</th>
                @endif
                @if($showCost)
                    <th>هزینه نهایی</th>
                @endif
                <th class="text-center">عملیات</th>
            </x-slot>

            <x-slot name="rows">
                @forelse($orders as $order)
                <tr class="group hover:bg-slate-50/80 transition-colors">
                    <td>
                        <span class="font-bold text-primary-600 block"><x-hash-ref :value="$order->tracking_code ?? $order->id" /></span>
                        <span class="text-xs text-slate-400 mt-1 block">
                            <x-jalali-date :value="$order->created_at" format="Y/m/d" />
                        </span>
                    </td>
                    <td>
                        @if($order->customer)
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700">{{ $order->customer->name }}</span>
                            <span class="text-xs text-slate-500">{{ $order->customer->phone }}</span>
                        </div>
                        @else
                        <span class="text-danger-500 text-xs">مشتری حذف شده</span>
                        @endif
                    </td>
                    <td>
                        @if($order->device)
                        <div class="flex items-center gap-2">
                            <i class="ti ti-device-laptop text-slate-400"></i>
                            <div class="flex flex-col">
                                <span class="font-medium text-slate-700">{{ $order->device->type ?: 'نامشخص' }}</span>
                                <span class="text-xs text-slate-500">{{ $order->device->model }}</span>
                            </div>
                        </div>
                        @else
                        <span class="text-danger-500 text-xs">دستگاه حذف شده</span>
                        @endif
                    </td>
                    <td>
                        <x-enhanced-status-badge :status="$order->status" />
                    </td>

                    @if($showTechnician)
                    <td>
                        @if($order->technician)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                <i class="ti ti-user text-xs"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-700">{{ $order->technician->name }}</span>
                        </div>
                        @else
                        <span class="text-xs text-slate-400 italic">تعیین نشده</span>
                        @endif
                    </td>
                    @endif

                    @if($showCost)
                    <td>
                        @php
                            $cost = 0;
                            if ($order->relationLoaded('repairItems')) {
                                $cost = $order->repairItems->sum(function ($item) {
                                    return $item->cost * $item->quantity;
                                });
                            } elseif ($order->service_cost) {
                                $cost = $order->service_cost;
                            }

                            if ($order->tax_amount) {
                                $cost += $order->tax_amount;
                            }
                        @endphp

                        @if($cost > 0)
                        <span class="font-bold text-emerald-600">{{ number_format($cost) }} تومان</span>
                        @else
                        <span class="text-xs text-slate-400">---</span>
                        @endif
                    </td>
                    @endif

                    <td>
                        <div class="flex items-center justify-center gap-2 flex-wrap">
                            <a href="{{ route('automation.repairs.show', $order) }}"
                               class="btn-modern btn-modern-secondary btn-sm"
                               title="مشاهده جزئیات">
                                <i class="ti ti-eye"></i>
                                مشاهده
                            </a>

                            @if($type === 'repair')
                                @if(!$order->technician_id && $order->status === \App\Enums\ServiceOrderStatus::REGISTERED)
                                <form action="{{ route('automation.repairs.assign-self', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-modern-warning btn-sm">
                                        <i class="ti ti-hand-stop"></i>
                                        تخصیص به من
                                    </button>
                                </form>
                                @elseif($order->technician_id === auth()->id() && $order->status === \App\Enums\ServiceOrderStatus::TECHNICIAN_ASSIGNED)
                                <form action="{{ route('automation.repairs.start', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-modern-success btn-sm">
                                        <i class="ti ti-player-play"></i>
                                        شروع تعمیر
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $colCount }}" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="ti ti-folder-off text-4xl mb-3 opacity-50"></i>
                            <p class="font-bold">هیچ سفارشی در این بخش یافت نشد.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </x-slot>

            @if($orders->hasPages())
            <x-slot name="pagination">
                {{ $orders->links() }}
            </x-slot>
            <x-slot name="total">{{ $orders->total() }}</x-slot>
            <x-slot name="from">{{ $orders->firstItem() }}</x-slot>
            <x-slot name="to">{{ $orders->lastItem() }}</x-slot>
            @endif
        </x-enhanced-table>
    </div>
</div>
@endsection
