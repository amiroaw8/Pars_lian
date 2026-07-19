@extends('layouts.admin')

@section('title', 'مدیریت مشتریان - پارس لیان')

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
            title="مدیریت مشتریان" 
            subtitle="مدیریت جامع اطلاعات تماس، سوابق سفارشات و دستگاه‌های متعلق به مشتریان پارس لیان."
            badge="بانک اطلاعاتی مشتریان"
            badgeIcon="ti-users"
            headerIcon="ti-user-search"
            actionUrl="{{ route('automation.customers.create') }}"
            actionText="ایجاد مشتری جدید"
        />

        <!-- Advanced Filter Card -->
        <x-enhanced-card variant="default" animated class="animate-slide-up mb-8">
            <form action="{{ route('automation.customers.index') }}" method="GET" class="p-2">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                    <div class="space-y-2">
                        <label for="search" class="text-xs font-black text-slate-500 uppercase tracking-widest mr-2">جستجو</label>
                        <div class="relative group">
                            <i class="ti ti-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="نام، تلفن یا ایمیل..." class="form-control-modern pr-12 w-full">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="trashed" class="text-xs font-black text-slate-500 uppercase tracking-widest mr-2">نمایش</label>
                        <select name="trashed" id="trashed" class="form-control-modern w-full">
                            <option value="">همه مشتریان فعال</option>
                            <option value="1" {{ request('trashed') == '1' ? 'selected' : '' }}>سطل زباله</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="date_from" class="text-xs font-black text-slate-500 uppercase tracking-widest mr-2">از تاریخ</label>
                        <input type="text" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control-modern w-full" placeholder="۱۴۰۴/۰۱/۰۱" dir="ltr">
                    </div>
                    <div class="space-y-2">
                        <label for="date_to" class="text-xs font-black text-slate-500 uppercase tracking-widest mr-2">تا تاریخ</label>
                        <input type="text" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control-modern w-full" placeholder="۱۴۰۴/۱۲/۲۹" dir="ltr">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-modern btn-modern-primary flex-1 py-3">
                            <i class="ti ti-filter text-lg"></i>
                            <span>اعمال فیلتر</span>
                        </button>
                        <a href="{{ route('automation.customers.index') }}" class="btn-modern btn-modern-light py-3 px-4" title="پاکسازی">
                            <i class="ti ti-refresh text-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </x-enhanced-card>

        <!-- Customer List Card -->
        <x-enhanced-card variant="default" animated class="animate-slide-up animation-delay-200">
            <div class="p-2">
                <div class="flex items-center justify-between gap-6 mb-8">
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-3">
                        <i class="ti ti-users text-primary-500"></i>
                        لیست مشتریان
                    </h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.recycle-bin.index', ['resource' => 'customers']) }}" class="btn-modern btn-modern-light py-2 px-4" title="سطل زباله">
                            <i class="ti ti-trash text-lg"></i>
                            <span class="mr-2">سطل زباله</span>
                        </a>
                        <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100 uppercase tracking-widest">تعداد کل: {{ $customers->total() }} نفر</span>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-2">
                    <x-enhanced-table striped hover responsive>
                        <x-slot name="headers">
                            <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-widest">نام مشتری</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-widest">شماره تماس</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-widest">آدرس</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest">دستگاه‌ها</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest">عملیات</th>
                        </x-slot>

                        <x-slot name="rows">
                            @forelse($customers as $customer)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-300 customer-row">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-50 to-primary-100 text-primary-600 flex items-center justify-center font-black text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            {{ mb_substr($customer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-900 group-hover:text-primary-600 transition-colors">{{ $customer->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">کد مشتری: <x-hash-ref :value="$customer->id" /></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <a href="tel:{{ $customer->phone }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-all font-black text-xs">
                                        <i class="ti ti-phone text-lg opacity-70"></i>
                                        {{ $customer->phone }}
                                    </a>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs text-slate-500 font-medium max-w-[200px] truncate" title="{{ $customer->address }}">
                                        {{ $customer->address ?? 'ثبت نشده' }}
                                    </p>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-black border border-blue-100 shadow-sm">
                                        <i class="ti ti-device-laptop"></i>
                                        {{ $customer->devices_count ?? 0 }} دستگاه
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($customer->trashed())
                                            <form action="{{ route('automation.customers.restore', $customer->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="w-10 h-10 flex items-center justify-center text-emerald-500 bg-emerald-50 hover:bg-emerald-500 hover:text-white rounded-xl transition-all shadow-sm" title="بازیابی">
                                                    <i class="ti ti-refresh text-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('automation.customers.force-delete', $customer->id) }}" method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="w-10 h-10 flex items-center justify-center text-danger-500 bg-danger-50 hover:bg-danger-500 hover:text-white rounded-xl transition-all shadow-sm btn-force-delete" data-item-name="{{ $customer->name }}" title="حذف دائمی">
                                                    <i class="ti ti-trash-x text-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('automation.customers.show', $customer) }}" class="w-10 h-10 flex items-center justify-center text-primary-500 bg-primary-50 hover:bg-primary-500 hover:text-white rounded-xl transition-all shadow-sm" title="مشاهده">
                                                <i class="ti ti-eye text-lg"></i>
                                            </a>
                                            <a href="{{ route('automation.customers.edit', $customer) }}" class="w-10 h-10 flex items-center justify-center text-warning-500 bg-warning-50 hover:bg-warning-500 hover:text-white rounded-xl transition-all shadow-sm" title="ویرایش">
                                                <i class="ti ti-edit text-lg"></i>
                                            </a>
                                            <form action="{{ route('automation.customers.destroy', $customer) }}" method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="w-10 h-10 flex items-center justify-center text-danger-500 bg-danger-50 hover:bg-danger-500 hover:text-white rounded-xl transition-all shadow-sm btn-delete" data-item-name="{{ $customer->name }}" title="حذف">
                                                    <i class="ti ti-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-6 animate-fade-in">
                                        <div class="w-24 h-24 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200">
                                            <i class="ti ti-users-off text-6xl"></i>
                                        </div>
                                        <div class="space-y-2">
                                            <p class="text-slate-900 font-black text-xl">هیچ مشتریی یافت نشد</p>
                                            <p class="text-slate-400 text-sm font-medium">هنوز هیچ مشتریی در سیستم ثبت نشده است.</p>
                                        </div>
                                        <a href="{{ route('automation.customers.create') }}" class="btn-modern btn-modern-primary py-3 px-8">
                                            <i class="ti ti-plus"></i>
                                            <span>ثبت اولین مشتری</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </x-slot>
                    </x-enhanced-table>
                </div>
                
                @if($customers->hasPages())
                <div class="mt-10 pt-10 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
                @endif
            </div>
        </x-enhanced-card>

        <!-- Help Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <i class="ti ti-user-plus text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 mb-1">ثبت سریع</h4>
                        <p class="text-slate-500 text-[11px] font-medium leading-relaxed">اطلاعات مشتری را برای مراجعات بعدی و گارانتی ثبت کنید.</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                        <i class="ti ti-history text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 mb-1">سوابق کامل</h4>
                        <p class="text-slate-500 text-[11px] font-medium leading-relaxed">تمامی سفارشات و تراکنش‌های مالی مشتری در پروفایل او ذخیره می‌شود.</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-500">
                        <i class="ti ti-devices text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 mb-1">مدیریت دستگاه‌ها</h4>
                        <p class="text-slate-500 text-[11px] font-medium leading-relaxed">هر مشتری می‌تواند چندین دستگاه با سوابق مجزا داشته باشد.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: `مشتری "${itemName}" به سطل زباله منتقل خواهد شد.`,
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
                text: `مشتری "${itemName}" برای همیشه حذف خواهد شد و قابل بازیابی نیست!`,
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
