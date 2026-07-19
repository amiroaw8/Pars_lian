@extends('layouts.admin')

@section('title', 'ویرایش کاربر - پنل مدیریت')

@php
    $routePrefix = 'super-admin.';
@endphp

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-orange-500/5 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="page-header animate-slide-up relative z-10">
        <div>
            <h1 class="page-title text-gradient flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-200">
                    <i class="ti ti-edit text-2xl"></i>
                </div>
                <span>ویرایش کاربر: {{ $user->name }}</span>
            </h1>
            <div class="breadcrumb text-secondary-600 mt-2">
                <a href="{{ route($routePrefix . 'dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="ti ti-smart-home"></i>
                    داشبورد
                </a>
                <i class="ti ti-chevron-left text-xs opacity-50"></i>
                <a href="{{ route($routePrefix . 'users.index') }}" class="hover:text-primary-600 transition-colors">کاربران</a>
                <i class="ti ti-chevron-left text-xs opacity-50"></i>
                <a href="{{ route($routePrefix . 'users.show', $user) }}" class="hover:text-primary-600 transition-colors">{{ $user->name }}</a>
                <i class="ti ti-chevron-left text-xs opacity-50"></i>
                <span class="text-slate-900 font-bold">ویرایش</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto pb-12 relative z-10">
        <x-enhanced-card icon="edit" title="ویرایش اطلاعات کاربر" animated class="animate-fade-in shadow-2xl shadow-amber-500/5 border-white/50 backdrop-blur-sm">
            <form method="POST" action="{{ route($routePrefix . 'users.update', $user) }}" class="space-y-8" id="userEditForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="form-group-modern group">
                        <label for="name" class="form-label-modern group-focus-within:text-amber-600">
                            <i class="ti ti-user text-lg"></i>
                            نام و نام خانوادگی
                        </label>
                        <div class="relative">
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required 
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-amber-500/10" placeholder="مثال: علی محمدی">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="ti ti-user-circle text-xl"></i>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="phone" class="form-label-modern group-focus-within:text-amber-600">
                            <i class="ti ti-phone text-lg"></i>
                            شماره تلفن
                        </label>
                        <div class="relative">
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required 
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-amber-500/10" placeholder="09123456789">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="ti ti-device-mobile text-xl"></i>
                            </div>
                        </div>
                        @error('phone')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="email" class="form-label-modern group-focus-within:text-amber-600">
                            <i class="ti ti-mail text-lg"></i>
                            ایمیل (اختیاری)
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-amber-500/10" placeholder="example@mail.com">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="ti ti-at text-xl"></i>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 form-group-modern group">
                        <label class="form-label-modern group-focus-within:text-amber-600 mb-4">
                            <i class="ti ti-shield-lock text-lg"></i>
                            نقش‌های کاربر (می‌توانید چندین نقش انتخاب کنید)
                        </label>
                        @include('admin.users.partials.role-picker', [
                            'roles' => $roles,
                            'selectedRoles' => old('role', $user->roles->pluck('name')->toArray()),
                            'locked' => $user->isSuperAdmin(),
                            'hiddenRoles' => $user->isSuperAdmin() ? $user->roles->pluck('name')->toArray() : [],
                        ])
                        @if($user->isSuperAdmin())
                            <p class="mt-4 text-[10px] text-amber-600 font-bold flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-lg border border-amber-100 w-fit">
                                <i class="ti ti-info-circle"></i>
                                نقش سوپر ادمین قابل تغییر نیست
                            </p>
                        @endif
                        @error('role')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="password" class="form-label-modern group-focus-within:text-amber-600">
                            <i class="ti ti-lock text-lg"></i>
                            رمز عبور جدید (اختیاری)
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-amber-500/10" placeholder="در صورت عدم نیاز خالی بگذارید">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="ti ti-key text-xl"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 font-bold flex items-center gap-1 px-1">
                            <i class="ti ti-help-circle"></i>
                            در صورت عدم نیاز به تغییر رمز عبور این فیلد را خالی بگذارید
                        </p>
                        @error('password')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="password_confirmation" class="form-label-modern group-focus-within:text-amber-600">
                            <i class="ti ti-lock-check text-lg"></i>
                            تکرار رمز عبور جدید
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-amber-500/10" placeholder="********">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                <i class="ti ti-circle-check text-xl"></i>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-slate-100">
                    <button type="submit" class="btn-modern btn-modern-primary py-4 px-10 flex-1 sm:flex-none justify-center text-lg shadow-xl shadow-amber-500/20 hover:shadow-amber-500/30 hover:-translate-y-1 transition-all">
                        <i class="ti ti-device-floppy text-xl"></i>
                        <span>ذخیره تغییرات</span>
                    </button>
                    <a href="{{ route($routePrefix . 'users.index') }}" class="btn-modern btn-modern-light py-4 px-10 flex-1 sm:flex-none justify-center text-slate-600 hover:bg-slate-100 transition-all">
                        <i class="ti ti-arrow-right text-xl"></i>
                        <span>انصراف و بازگشت</span>
                    </a>
                </div>
            </form>
        </x-enhanced-card>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userEditForm');
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = `
                <i class="ti ti-loader-2 animate-spin text-xl"></i>
                <span>در حال بروزرسانی...</span>
            `;
        });
    });
</script>
@endpush
@endsection
