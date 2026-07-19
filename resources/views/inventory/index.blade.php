@extends('layouts.admin')

@section('title', 'مدیریت انبار - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <x-page-header
            class="w-full"
            title="مدیریت انبار"
            subtitle="مدیریت جامع موجودی قطعات، دستگاه‌ها و ابزارهای مورد نیاز برای فرآیند تعمیرات."
            badge="کنترل موجودی"
            badgeIcon="ti-package"
            headerIcon="ti-box"
            actionUrl="{{ route('automation.inventory.create') }}"
            actionText="افزودن کالای جدید"
        >
            <x-slot:extraActions>
                <a href="{{ route('automation.inventory.reports.index') }}" class="btn-modern btn-modern-warning py-3 px-6 shadow-lg shadow-warning-500/20 group">
                    <i class="ti ti-chart-pie group-hover:scale-110 transition-transform"></i>
                    <span>گزارش‌های انبار</span>
                </a>
            </x-slot>
        </x-page-header>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form action="{{ route('automation.inventory.index') }}" method="GET" class="relative w-full sm:max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو (نام، SKU، کد دستگاه)..." class="pl-10 pr-4 py-2 rounded-xl border-none bg-white/50 focus:bg-white focus:ring-2 focus:ring-primary-500 transition-all text-sm w-full shadow-sm">
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-500 transition-colors">
                    <i class="ti ti-search"></i>
                </button>
                @if(request('trashed'))
                    <input type="hidden" name="trashed" value="1">
                @endif
            </form>
            <a href="{{ route('automation.inventory.index', request()->has('trashed') ? [] : ['trashed' => 1]) }}"
               class="btn-modern {{ request()->has('trashed') ? 'btn-modern-primary' : 'btn-modern-light' }} py-2 px-4 text-xs shrink-0 w-full sm:w-auto justify-center">
                <i class="ti ti-trash{{ request()->has('trashed') ? '-off' : '' }} text-lg"></i>
                {{ request()->has('trashed') ? 'نمایش کالاهای موجود' : 'سطل زباله' }}
            </a>
        </div>

        <x-enhanced-card variant="default" animated class="animate-fade-in">
            <div class="table-container">
                <x-enhanced-table striped hover responsive>
                    <x-slot name="headers">
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>نام کالا</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'name' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'name' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sku', 'direction' => request('sort') == 'sku' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>شناسه (SKU/Device)</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'sku' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'sku' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>نوع کالا</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'type' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'type' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'condition', 'direction' => request('sort') == 'condition' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>وضعیت</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'condition' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'condition' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'direction' => request('sort') == 'quantity' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>موجودی</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'quantity' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'quantity' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'min_quantity', 'direction' => request('sort') == 'min_quantity' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span class="text-[11px] leading-tight">حداقل موجودی جهت هشدار</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'min_quantity' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'min_quantity' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => request('sort') == 'price' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>قیمت واحد</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'price' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'price' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="p-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'color', 'direction' => request('sort') == 'color' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group w-full h-full p-4 hover:bg-slate-100 transition-colors">
                                <span>رنگ</span>
                                <div class="flex flex-col text-[8px] text-slate-400 group-hover:text-primary-500">
                                    <i class="ti ti-caret-up {{ request('sort') == 'color' && request('direction') == 'asc' ? 'text-primary-600' : '' }}"></i>
                                    <i class="ti ti-caret-down {{ request('sort') == 'color' && request('direction') == 'desc' ? 'text-primary-600' : '' }}"></i>
                                </div>
                            </a>
                        </th>
                        <th class="text-center w-24">عملیات</th>
                    </x-slot>
                    
                    <x-slot name="rows">
                        @forelse($inventories as $inventory)
                        <tr class="hover-lift group">
                            <td>
                                <div class="flex flex-col">
                                    <strong class="text-slate-800 group-hover:text-primary-600 transition-colors">{{ $inventory->name }}</strong>
                                    @if($inventory->description)
                                        <span class="text-[10px] text-slate-400 truncate max-w-[200px]">{{ Str::limit($inventory->description, 50) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    @if($inventory->device_code)
                                        <span class="text-[10px] font-mono bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded w-fit flex items-center gap-1" title="کد دستگاه">
                                            <i class="ti ti-scan"></i> {{ $inventory->device_code }}
                                        </span>
                                    @endif
                                    @if($inventory->sku)
                                        <span class="text-[10px] font-mono text-slate-500" title="SKU">
                                            SKU: {{ $inventory->sku }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($inventory->type == 'device')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="ti ti-device-laptop"></i>
                                        دستگاه
                                    </span>
                                @elseif($inventory->type == 'part')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-puzzle"></i>
                                        قطعه
                                    </span>
                                @elseif($inventory->type == 'tool')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-tools"></i>
                                        ابزار
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        سایر
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($inventory->condition == 'new')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-50 text-teal-700 border border-teal-200">
                                        <i class="ti ti-sparkles"></i>
                                        نو
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-recycle"></i>
                                        دست دوم
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($inventory->quantity == 0)
                                        <span class="text-lg font-black text-danger-600">0</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-danger-50 text-danger-600 border border-danger-100">
                                            <i class="ti ti-alert-circle"></i>
                                            اتمام
                                        </span>
                                    @elseif($inventory->quantity <= $inventory->min_quantity)
                                        <span class="text-lg font-black text-warning-600">{{ $inventory->quantity }}</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-warning-50 text-warning-600 border border-warning-100">
                                            <i class="ti ti-alert-triangle"></i>
                                            کم
                                        </span>
                                    @else
                                        <span class="text-lg font-black text-success-600">{{ $inventory->quantity }}</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-success-50 text-success-600 border border-success-100">
                                            <i class="ti ti-check"></i>
                                            کافی
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-sm font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg border border-amber-100">
                                    {{ $inventory->min_quantity }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-primary-600 font-black">
                                    {{ number_format($inventory->price) }} <span class="text-[10px] font-medium opacity-70">تومان</span>
                                </strong>
                            </td>
                            <td>
                                @if($inventory->color)
                                    <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                        {{ $inventory->color }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    @if($inventory->trashed())
                                        <form action="{{ route('automation.inventory.restore', $inventory) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-success-500 hover:bg-success-50 rounded-xl transition-all" title="بازیابی">
                                                <i class="ti ti-rotate-clockwise text-lg"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('automation.inventory.force-delete', $inventory) }}" method="POST" class="inline force-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="p-2 text-danger-500 hover:bg-danger-50 rounded-xl transition-all btn-force-delete" 
                                                    title="حذف دائمی" data-item-name="{{ $inventory->name }}">
                                                <i class="ti ti-trash-x text-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('automation.inventory.show', $inventory) }}" class="p-2 text-primary-500 hover:bg-primary-50 rounded-xl transition-all" title="مشاهده">
                                            <i class="ti ti-eye text-lg"></i>
                                        </a>
                                        
                                        <a href="{{ route('automation.inventory.edit', $inventory) }}" class="p-2 text-warning-500 hover:bg-warning-50 rounded-xl transition-all" title="ویرایش">
                                            <i class="ti ti-edit text-lg"></i>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('automation.inventory.destroy', $inventory) }}" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="p-2 text-danger-500 hover:bg-danger-50 rounded-xl transition-all btn-delete" 
                                                    title="حذف" 
                                                    data-item-name="{{ $inventory->name }}">
                                                <i class="ti ti-trash text-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-20">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mb-4">
                                        <i class="ti ti-package-off text-4xl text-slate-300"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium mb-4">هیچ کالایی در انبار یافت نشد</p>
                                    <a href="{{ route('automation.inventory.create') }}" class="btn-modern btn-modern-primary">
                                        <i class="ti ti-plus"></i>
                                        افزودن اولین کالا
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-enhanced-table>
            </div>
            
            @if(method_exists($inventories, 'links') && $inventories->hasPages())
            <div class="mt-8">
                {{ $inventories->links() }}
            </div>
            @endif
        </x-enhanced-card>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search loading state
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const btn = this.querySelector('.search-btn');
            const icon = this.querySelector('.search-icon');
            const loading = this.querySelector('.search-loading');
            
            if (btn && icon && loading) {
                btn.classList.add('cursor-not-allowed', 'opacity-75');
                icon.classList.add('hidden');
                loading.classList.remove('hidden');
            }
        });
    }

    const deleteButtons = document.querySelectorAll('.btn-delete');
    const forceDeleteButtons = document.querySelectorAll('.btn-force-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'آیا اطمینان دارید؟',
                text: `کالای "${itemName}" به سطل زباله منتقل خواهد شد.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    container: 'font-vazir',
                    popup: 'rounded-[2rem]',
                    confirmButton: 'btn-modern btn-modern-warning px-6 py-2',
                    cancelButton: 'btn-modern btn-modern-light px-6 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    forceDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'حذف دائمی کالا',
                text: `آیا از حذف دائمی "${itemName}" اطمینان دارید؟ این عمل غیرقابل بازگشت است!`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، برای همیشه حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    container: 'font-vazir',
                    popup: 'rounded-[2rem]',
                    confirmButton: 'btn-modern btn-modern-danger px-6 py-2',
                    cancelButton: 'btn-modern btn-modern-light px-6 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
    .font-vazir { font-family: 'Vazirmatn', sans-serif !important; }
</style>
@endsection
