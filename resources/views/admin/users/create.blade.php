@extends('layouts.admin')

@section('title', 'افزودن کاربر جدید - پنل مدیریت')

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
        <div>
            <h1 class="page-title text-gradient flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="ti ti-user-plus text-2xl"></i>
                </div>
                <span>افزودن کاربر جدید</span>
            </h1>
            <div class="breadcrumb text-secondary-600 mt-2">
                <a href="{{ route($routePrefix . 'dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="ti ti-smart-home"></i>
                    داشبورد
                </a>
                <i class="ti ti-chevron-left text-xs opacity-50"></i>
                <a href="{{ route($routePrefix . 'users.index') }}" class="hover:text-primary-600 transition-colors">کاربران</a>
                <i class="ti ti-chevron-left text-xs opacity-50"></i>
                <span class="text-slate-900 font-bold">افزودن جدید</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto pb-12 relative z-10">
        <x-enhanced-card icon="user-plus" title="اطلاعات کاربر جدید" animated class="animate-fade-in shadow-2xl shadow-blue-500/5 border-white/50 backdrop-blur-sm">
            <form method="POST" action="{{ route($routePrefix . 'users.store') }}" class="space-y-8" id="userCreateForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="form-group-modern group">
                        <label for="name" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-user text-lg"></i>
                            نام و نام خانوادگی
                        </label>
                        <div class="relative">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-blue-500/10" placeholder="مثال: علی محمدی">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
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
                        <label for="phone" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-phone text-lg"></i>
                            شماره تلفن
                        </label>
                        <div class="relative">
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required 
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-blue-500/10" placeholder="09123456789">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
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
                        <label for="email" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-mail text-lg"></i>
                            ایمیل (اختیاری)
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-blue-500/10" placeholder="example@mail.com">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
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
                        <label class="form-label-modern group-focus-within:text-blue-600 mb-4">
                            <i class="ti ti-shield-lock text-lg"></i>
                            نقش‌های کاربر (می‌توانید چندین نقش انتخاب کنید)
                        </label>
                        @include('admin.users.partials.role-picker', [
                            'roles' => $roles,
                            'selectedRoles' => old('role', []),
                        ])
                        @error('role')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="password" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-lock text-lg"></i>
                            رمز عبور
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-blue-500/10" placeholder="********">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <i class="ti ti-key text-xl"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1 animate-shake">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group-modern group">
                        <label for="password_confirmation" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-lock-check text-lg"></i>
                            تکرار رمز عبور
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="form-control-modern pr-12 focus:ring-4 focus:ring-blue-500/10" placeholder="********">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
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
                    <button type="submit" class="btn-modern btn-modern-primary py-4 px-10 flex-1 sm:flex-none justify-center text-lg shadow-xl shadow-blue-500/20 hover:shadow-blue-500/30 hover:-translate-y-1 transition-all">
                        <i class="ti ti-device-floppy text-xl"></i>
                        <span>ذخیره و ثبت کاربر</span>
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
        const form = document.getElementById('userCreateForm');
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = `
                <i class="ti ti-loader-2 animate-spin text-xl"></i>
                <span>در حال ثبت...</span>
            `;
        });
    });
</script>
@endpush
@endsection
