@extends('layouts.admin')

@section('title', 'مشاهده کاربر - پنل مدیریت')

@php
    $routePrefix = 'super-admin.';
@endphp

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/5 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="page-header animate-slide-up relative z-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="page-title text-gradient flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 flex items-center justify-center text-white shadow-lg shadow-slate-200">
                        <i class="ti ti-user text-2xl"></i>
                    </div>
                    <span>مشاهده کاربر: {{ $user->name }}</span>
                </h1>
                <div class="breadcrumb text-secondary-600 mt-2">
                    <a href="{{ route($routePrefix . 'dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                        <i class="ti ti-smart-home"></i>
                        داشبورد
                    </a>
                    <i class="ti ti-chevron-left text-xs opacity-50"></i>
                    <a href="{{ route($routePrefix . 'users.index') }}" class="hover:text-primary-600 transition-colors">کاربران</a>
                    <i class="ti ti-chevron-left text-xs opacity-50"></i>
                    <span class="text-slate-900 font-bold">{{ $user->name }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route($routePrefix . 'users.edit', $user) }}" class="btn-modern btn-modern-warning py-2.5 px-5 shadow-lg shadow-amber-500/10">
                    <i class="ti ti-edit"></i>
                    <span>ویرایش کاربر</span>
                </a>
                @if(!$user->isSuperAdmin() && $user->id !== \Illuminate\Support\Facades\Auth::id())
                <form method="POST" action="{{ route($routePrefix . 'users.toggle-status', $user) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-modern {{ !$user->is_active ? 'btn-modern-success' : 'btn-modern-warning' }} py-2.5 px-5 shadow-lg shadow-{{ !$user->is_active ? 'emerald' : 'amber' }}-500/10">
                        <i class="ti {{ !$user->is_active ? 'ti-user-check' : 'ti-user-x' }}"></i>
                        <span>{{ !$user->is_active ? 'فعال کردن' : 'غیرفعال کردن' }}</span>
                    </button>
                </form>
                @endif
                <a href="{{ route($routePrefix . 'users.index') }}" class="btn-modern btn-modern-light py-2.5 px-5 text-slate-600 hover:bg-slate-100 transition-all">
                    <i class="ti ti-arrow-right"></i>
                    <span>بازگشت به لیست</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10 pb-12">
        <!-- User Information -->
        <div class="lg:col-span-2 space-y-8">
            <x-enhanced-card title="اطلاعات کاربر" icon="user-circle" class="animate-fade-in shadow-2xl shadow-slate-200/40">
                <div class="flex flex-col md:flex-row items-center gap-10 mb-12 p-8 bg-gradient-to-br from-slate-50 to-white rounded-[2.5rem] border border-slate-100 shadow-inner">
                    <div class="relative group">
                        <div class="w-40 h-40 bg-white rounded-[2.5rem] flex items-center justify-center text-6xl text-primary-600 shadow-2xl border-4 border-white group-hover:scale-105 transition-all duration-500 group-hover:rotate-3">
                            @php
                                $initials = collect(explode(' ', $user->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                            @endphp
                            <span class="font-black tracking-tighter">{{ $initials ?: 'U' }}</span>
                        </div>
                        <div class="absolute -bottom-3 -right-3 w-12 h-12 bg-primary-600 text-white rounded-2xl flex items-center justify-center shadow-xl animate-bounce">
                            <i class="ti ti-shield-check text-2xl"></i>
                        </div>
                    </div>
                    
                    <div class="text-center md:text-right space-y-4">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                        <div class="flex flex-wrap justify-center md:justify-start gap-3">
                            <x-user-role-badges :user="$user" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                        <div class="relative z-10">
                            <div class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-primary-500 transition-colors">شماره تلفن همراه</div>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="ti ti-phone"></i>
                                </div>
                                {{ $user->phone }}
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-blue-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                        <div class="relative z-10">
                            <div class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-primary-500 transition-colors">نشانی ایمیل</div>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="ti ti-mail"></i>
                                </div>
                                <span class="truncate" title="{{ $user->email }}">{{ $user->email ?? 'ثبت نشده' }}</span>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-indigo-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                        <div class="relative z-10">
                            <div class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-primary-500 transition-colors">سطح دسترسی</div>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <i class="ti ti-shield-lock"></i>
                                </div>
                                {{ $user->getRoleDisplayName() }}
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-amber-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                        <div class="relative z-10">
                            <div class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-primary-500 transition-colors">تاریخ پیوستن</div>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="ti ti-calendar-event"></i>
                                </div>
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($user->created_at)->format('Y/m/d') }}
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-emerald-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            </x-enhanced-card>

            @if(!$user->isSuperAdmin() && $user->id !== \Illuminate\Support\Facades\Auth::id())
            <x-enhanced-card title="مدیریت حساب کاربری" icon="settings" class="border-rose-100 bg-rose-50/30">
                <div class="p-8 bg-white rounded-[2rem] border border-rose-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-50"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="flex items-start gap-5">
                            <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                                <i class="ti ti-trash-x text-3xl"></i>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-rose-900 font-black text-xl">حذف کامل حساب کاربری</h4>
                                <p class="text-rose-700/60 text-sm leading-relaxed max-w-md">
                                    با حذف این کاربر، تمامی دسترسی‌های او به سیستم قطع شده و اطلاعات او از دیتابیس حذف خواهد شد. این عمل غیرقابل بازگشت است.
                                </p>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route($routePrefix . 'users.destroy', $user) }}" 
                              class="w-full md:w-auto delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn-modern bg-rose-600 hover:bg-rose-700 text-white w-full md:w-auto py-4 px-10 justify-center shadow-xl shadow-rose-200 hover:-translate-y-1 transition-all btn-delete"
                                    data-user-name="{{ $user->name }}">
                                <i class="ti ti-trash"></i>
                                <span>حذف دائمی کاربر</span>
                            </button>
                        </form>
                    </div>
                </div>
            </x-enhanced-card>
            @endif
        </div>

        <!-- Recent Activities -->
        <div class="space-y-8">
            <x-enhanced-card title="فعالیت‌های اخیر" icon="activity" class="animate-fade-in shadow-2xl shadow-slate-200/40">
                @php
                    $recentActivities = \App\Models\OrderLog::where('user_id', $user->id)
                                            ->with('serviceOrder')
                                            ->latest()
                                            ->take(10)
                                            ->get();
                @endphp

                @if($recentActivities->isNotEmpty())
                    <div class="space-y-8 relative before:absolute before:right-[1.375rem] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        @foreach($recentActivities as $activity)
                        <div class="relative pr-12 group">
                            <div class="absolute right-4 top-1.5 w-3 h-3 rounded-full border-2 border-white shadow-sm transition-all duration-300 group-hover:scale-150 group-hover:shadow-lg z-10
                                @switch($activity->action)
                                    @case('created') bg-emerald-500 shadow-emerald-200 @break
                                    @case('updated') bg-blue-500 shadow-blue-200 @break
                                    @case('attachment_added') bg-purple-500 shadow-purple-200 @break
                                    @default bg-slate-400 shadow-slate-200
                                @endswitch
                            "></div>
                            
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 group-hover:bg-white group-hover:shadow-xl group-hover:border-transparent transition-all duration-300">
                                <div class="text-slate-800 text-sm font-bold mb-2 leading-relaxed group-hover:text-primary-600 transition-colors">{{ $activity->description }}</div>
                                <div class="flex items-center justify-between mt-4">
                                    <a href="{{ route('automation.service-orders.show', $activity->service_order_id) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2.5 py-1 rounded-lg hover:bg-blue-600 hover:text-white transition-all border border-blue-100">
                                        سفارش <x-hash-ref :value="$activity->service_order_id" />
                                    </a>
                                    <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                        <i class="ti ti-clock-hour-4"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-10 pt-6 border-t border-slate-50 text-center">
                        <button class="text-primary-600 text-[10px] font-black hover:text-primary-700 transition-all uppercase tracking-[0.2em] flex items-center justify-center gap-2 w-full group">
                            <span>مشاهده تمامی فعالیت‌ها</span>
                            <i class="ti ti-chevron-down group-hover:translate-y-0.5 transition-transform"></i>
                        </button>
                    </div>
                @else
                    <div class="py-20 flex flex-col items-center justify-center text-slate-400">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <i class="ti ti-ghost text-5xl opacity-20"></i>
                        </div>
                        <p class="font-black text-sm text-slate-500">فعالیتی ثبت نشده است</p>
                        <p class="text-xs text-slate-400 mt-2">این کاربر هنوز فعالیتی در سیستم نداشته است.</p>
                    </div>
                @endif
            </x-enhanced-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete button logic
    const deleteBtn = document.querySelector('.btn-delete');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const userName = this.getAttribute('data-user-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'آیا اطمینان دارید؟',
                text: `حساب کاربری «${userName}» به همراه تمامی دسترسی‌ها حذف خواهد شد. این عمل غیرقابل بازگشت است!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، حذف شود',
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
    }
});
</script>
@endpush
@endsection
