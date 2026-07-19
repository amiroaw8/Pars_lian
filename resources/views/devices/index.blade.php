@extends('layouts.admin')

@section('title', 'مدیریت دستگاه‌ها - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-12">
            <x-page-header 
                title="مدیریت دستگاه‌ها" 
                subtitle="بانک جامع اطلاعات فنی دستگاه‌های مشتریان، سوابق تعمیراتی و وضعیت گارانتی آن‌ها."
                badge="Technical Database"
                badgeIcon="ti-devices"
                headerIcon="ti-device-laptop"
                actionUrl="{{ route('automation.devices.create') }}"
                actionText="ثبت دستگاه جدید"
            />
            
            <div class="flex items-center gap-2">
                <a href="{{ route('automation.devices.index', request()->has('trashed') ? [] : ['trashed' => 1]) }}" 
                   class="btn-modern {{ request()->has('trashed') ? 'btn-modern-primary' : 'btn-modern-light' }} py-2 px-4 text-xs">
                    <i class="ti ti-trash{{ request()->has('trashed') ? '-off' : '' }} text-lg"></i>
                    {{ request()->has('trashed') ? 'نمایش دستگاه‌های موجود' : 'سطل زباله' }}
                </a>
            </div>
        </div>

        <x-enhanced-card variant="default" animated class="animate-slide-up">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div class="relative flex-1 max-w-md group">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                        <i class="ti ti-search text-xl"></i>
                    </span>
                    <input type="text" id="deviceSearch" class="form-control-modern pr-12 w-full" placeholder="جستجوی مدل، نوع یا مشتری...">
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 text-sm text-slate-500 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                            <i class="ti ti-info-circle"></i>
                        </div>
                        <span class="font-black">تعداد کل دستگاه‌ها: <span class="text-primary-600 ml-1">{{ $devices->total() }}</span></span>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <x-enhanced-table>
                    <x-slot name="headers">
                        <th class="text-right">اطلاعات دستگاه</th>
                        <th class="text-right">دسته‌بندی</th>
                        <th class="text-right">مالک (مشتری)</th>
                        <th class="text-right">شماره اموال / سریال</th>
                        <th class="text-right">وضعیت گارانتی</th>
                        <th class="text-center">عملیات</th>
                    </x-slot>
                    
                    <x-slot name="rows">
                        @forelse($devices as $device)
                        <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white group-hover:scale-110 transition-all duration-500 shadow-sm">
                                        <i class="ti ti-{{ $device->type == 'laptop' ? 'device-laptop' : ($device->type == 'mobile' ? 'device-mobile' : 'device-desktop') }} text-2xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 group-hover:text-primary-600 transition-colors">{{ $device->model }}</div>
                                        <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-0.5">ID: <x-hash-ref :value="$device->id" /></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider border border-slate-200/50 group-hover:bg-white transition-colors">
                                    <i class="ti ti-category-2 text-sm"></i>
                                    {{ $device->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($device->customer)
                                <a href="{{ route('automation.customers.show', $device->customer) }}" class="flex items-center gap-3 group/link">
                                    <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xs font-black border border-primary-100 group-hover/link:bg-primary-600 group-hover/link:text-white transition-all shadow-sm">
                                        {{ mb_substr($device->customer->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-700 group-hover/link:text-primary-600 transition-colors">{{ $device->customer->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $device->customer->phone }}</span>
                                    </div>
                                </a>
                                @else
                                    <div class="flex items-center gap-3 text-slate-300">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                            <i class="ti ti-user-x text-lg"></i>
                                        </div>
                                        <span class="text-xs font-bold italic">بدون مالک</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($device->asset_number || $device->serial_number)
                                    <div class="flex flex-col gap-1">
                                        @if($device->asset_number)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-black border border-blue-100/50">
                                                <i class="ti ti-hash text-xs"></i>
                                                {{ $device->asset_number }}
                                            </span>
                                        @endif
                                        @if($device->serial_number)
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $device->serial_number }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300">---</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($device->has_guarantee)
                                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-2xl bg-emerald-50 text-emerald-600 text-[10px] font-black border border-emerald-100/50 shadow-sm shadow-emerald-500/5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        دارای گارانتی
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-2xl bg-slate-50 text-slate-400 text-[10px] font-black border border-slate-100">
                                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                        بدون گارانتی
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($device->trashed())
                                        <form action="{{ route('automation.devices.restore', $device->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-10 h-10 rounded-xl bg-success-50 text-success-600 flex items-center justify-center hover:bg-success-600 hover:text-white transition-all shadow-sm" title="بازیابی">
                                                <i class="ti ti-rotate-clockwise text-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('automation.devices.force-delete', $device->id) }}" method="POST" class="inline force-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="w-10 h-10 rounded-xl bg-danger-50 text-danger-600 flex items-center justify-center hover:bg-danger-600 hover:text-white transition-all shadow-sm btn-force-delete" 
                                                    title="حذف دائمی" data-item-name="{{ $device->model }}">
                                                <i class="ti ti-trash-x text-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('automation.devices.show', $device) }}" class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all shadow-sm shadow-primary-100" title="مشاهده جزئیات">
                                            <i class="ti ti-eye text-lg"></i>
                                        </a>
                                        <a href="{{ route('automation.devices.edit', $device) }}" class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all shadow-sm shadow-amber-100" title="ویرایش">
                                            <i class="ti ti-edit text-lg"></i>
                                        </a>
                                        <form method="POST" action="{{ route('automation.devices.destroy', $device) }}" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="w-10 h-10 rounded-xl bg-danger-50 text-danger-600 flex items-center justify-center hover:bg-danger-600 hover:text-white transition-all shadow-sm btn-delete" 
                                                    title="حذف" data-item-name="{{ $device->model }}">
                                                <i class="ti ti-trash text-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-24">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="relative mb-8">
                                        <div class="w-24 h-24 rounded-[2.5rem] bg-slate-50 flex items-center justify-center text-slate-300 animate-pulse">
                                            <i class="ti ti-device-laptop text-5xl"></i>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-2xl shadow-xl flex items-center justify-center text-primary-500 animate-bounce">
                                            <i class="ti ti-plus text-xl"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 mb-2">هیچ دستگاهی ثبت نشده است</h3>
                                    <p class="text-slate-500 text-sm font-medium leading-relaxed mb-8">هنوز هیچ دستگاهی در سامانه ثبت نشده است. برای شروع می‌توانید اولین دستگاه را ثبت کنید.</p>
                                    <a href="{{ route('automation.devices.create') }}" class="btn-modern btn-modern-primary py-3 px-8">
                                        ثبت اولین دستگاه
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-enhanced-table>
            </div>
            
            @if($devices->hasPages())
            <div class="mt-10 pt-10 border-t border-slate-100">
                {{ $devices->links() }}
            </div>
            @endif
        </x-enhanced-card>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('deviceSearch');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        tableRows.forEach(row => {
            if (row.cells.length <= 1) return; // Skip empty row
            
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                row.classList.add('animate-fade-in');
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: `دستگاه "${itemName}" به سطل زباله منتقل خواهد شد.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    confirmButton: 'font-black',
                    cancelButton: 'font-black'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Force delete confirmation
    document.querySelectorAll('.btn-force-delete').forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'حذف دائمی؟',
                text: `دستگاه "${itemName}" برای همیشه حذف خواهد شد و قابل بازیابی نیست!`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، برای همیشه حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    confirmButton: 'font-black',
                    cancelButton: 'font-black'
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
@endpush
@endsection
