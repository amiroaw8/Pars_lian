@extends('layouts.admin')

@section('title', 'میز کار - پارس لیان')

@section('content')
    <div class="page-header animate-slide-up">
        <div>
            <h1 class="page-title text-gradient">
                <i class="ti ti-layout-dashboard"></i>
                خوش آمدید، {{ auth()->user()->name }}
            </h1>
            <x-dashboard-breadcrumb :sections="$activeWorkSections ?? []" />
        </div>
    </div>

    <div class="mb-8">
        <x-active-work-panel
            :sections="$activeWorkSections ?? []"
            :poll-url="route('automation.dashboard.active-work')"
        />
    </div>

    <div class="mb-8">
        <x-dashboard-notifications />
    </div>

    <div class="mb-8">
        {{-- بخش دسترسی سریع بر اساس نقش --}}
        @php
            $actions = [];
            $user = auth()->user();

            if ($user->isTechnician()) {
                $actions = [
                    ['label' => 'لیست تعمیرات', 'icon' => 'tool', 'url' => route('automation.repairs.index')],
                    ['label' => 'ثبت قطعه مصرفی', 'icon' => 'package-plus', 'url' => route('automation.inventory.index')],
                ];
            } elseif ($user->isReceptionist()) {
                $actions = [
                    ['label' => 'ثبت مشتری', 'icon' => 'user-plus', 'url' => route('automation.customers.create')],
                    ['label' => 'ثبت پذیرش', 'icon' => 'file-plus', 'url' => route('automation.service-orders.create')],
                    ['label' => 'لیست مشتریان', 'icon' => 'users', 'url' => route('automation.customers.index')],
                ];
            } elseif ($user->isWarehouseManager()) {
                $actions = [
                    ['label' => 'لیست کالاها', 'icon' => 'box', 'url' => route('automation.inventory.index')],
                    ['label' => 'افزودن کالا', 'icon' => 'circle-plus', 'url' => route('automation.inventory.create')],
                    ['label' => 'گزارش موجودی', 'icon' => 'file-analytics', 'url' => route('automation.inventory.reports.index')],
                ];
            } elseif ($user->isAccountant()) {
                $actions = [
                    ['label' => 'ثبت فروش', 'icon' => 'shopping-cart-plus', 'url' => route('automation.accounting.create-sale')],
                    ['label' => 'گزارشات مالی', 'icon' => 'report-money', 'url' => route('automation.accounting.index')],
                    ['label' => 'لیست فاکتورها', 'icon' => 'receipt', 'url' => route('automation.accounting.index')],
                ];
            } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
                $actions = [
                    ['label' => 'مدیریت کاربران', 'icon' => 'user-edit', 'url' => route('super-admin.users.index')],
                    ['label' => 'گزارشات انبار', 'icon' => 'file-analytics', 'url' => route('automation.inventory.reports.index')],
                    ['label' => 'لیست کالاها', 'icon' => 'box', 'url' => route('automation.inventory.index')],
                    ['label' => 'تنظیمات سیستم', 'icon' => 'settings', 'url' => route('admin.settings.index')],
                    ['label' => 'سامانه پیامک', 'icon' => 'message-dots', 'url' => route('admin.sms.dashboard')],
                ];
            }
        @endphp

        @if(count($actions) > 0)
            <x-quick-actions :actions="$actions" />
        @endif
    </div>

    <div class="grid grid-cols-1 gap-8 mb-8">
        {{-- بخش اختصاصی بر اساس نقش --}}

        @if(auth()->user()->isTechnician())
            <div class="grid grid-cols-1 gap-8">
                <x-cells.repair-cell />
            </div>
        @endif

        @if(auth()->user()->isReceptionist())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <x-cells.repair-cell />
                <x-cells.sales-cell />
            </div>
        @endif

        @if(auth()->user()->isWarehouseManager())
            <div class="grid grid-cols-1 gap-8">
                <x-cells.warehouse-cell />
            </div>
        @endif

        @if(auth()->user()->isAccountant())
            <div class="grid grid-cols-1 gap-8">
                <x-cells.accounting-cell />
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <x-cells.repair-cell />
                <x-cells.warehouse-cell />
                <x-cells.accounting-cell />
                <x-cells.sales-cell />
            </div>
        @endif
    </div>
@endsection