@extends('layouts.admin')

@section('title', 'مدیریت کاربران - پنل مدیریت')

@php
    $routePrefix = 'super-admin.';
@endphp

@section('content')
<div class="animate-fade-in space-y-6">
    <!-- Header Section -->
    <x-page-header 
        title="مدیریت کاربران سیستم" 
        subtitle="مدیریت و نظارت بر دسترسی‌های کاربران، نقش‌های سیستمی و اطلاعات تماس اعضای تیم."
        badge="User Management"
        badgeIcon="ti-users"
        headerIcon="ti-users"
        actionUrl="{{ route($routePrefix . 'users.create') }}"
        actionText="افزودن کاربر جدید"
        class="mb-8"
    >
        <x-slot name="extraActions">
            @if(request('trashed'))
                <a href="{{ route($routePrefix . 'users.index') }}" class="btn-modern btn-modern-light">
                    <i class="ti ti-users"></i>
                    <span>مشاهده کاربران فعال</span>
                </a>
            @else
                <a href="{{ route($routePrefix . 'users.index', ['trashed' => 1]) }}" class="btn-modern btn-modern-light">
                    <i class="ti ti-trash"></i>
                    <span>سطل زباله</span>
                </a>
            @endif
        </x-slot>
    </x-page-header>

    <!-- Users Table Card -->
    <x-enhanced-card animated class="animate-slide-up">
        <x-slot name="header">
            <div class="flex items-center gap-3">
                <i class="ti ti-list text-primary-600 text-xl"></i>
                <h3 class="card-title text-slate-800 font-bold">لیست کاربران سیستم</h3>
            </div>
        </x-slot>
        <x-slot name="headerAction">
            <div class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-sm font-bold border border-blue-100 shadow-sm">
                {{ $users->total() }} کاربر
            </div>
        </x-slot>

        <div class="table-responsive">
            <x-enhanced-table>
                <x-slot name="headers">
                    <th class="whitespace-nowrap">نام کاربر</th>
                    <th class="whitespace-nowrap">اطلاعات تماس</th>
                    <th class="whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-shield-check text-slate-400"></i>
                            <span>نقش سیستم</span>
                        </div>
                    </th>
                    <th class="whitespace-nowrap text-center">تاریخ عضویت</th>
                    <th class="whitespace-nowrap text-center">عملیات</th>
                </x-slot>
                <x-slot name="rows">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-200 {{ $user->trashed() ? 'bg-rose-50/30' : '' }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600 flex items-center justify-center font-bold shadow-sm border border-slate-200/50">
                                        @php
                                            $initials = '';
                                            $nameParts = explode(' ', $user->name);
                                            if (count($nameParts) >= 1) {
                                                $initials = mb_substr($nameParts[0], 0, 1);
                                                if (count($nameParts) >= 2) {
                                                    $initials .= mb_substr($nameParts[1], 0, 1);
                                                }
                                            }
                                        @endphp
                                        {{ $initials ?: 'U' }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $user->name }}</span>
                                        <div class="flex gap-1 mt-0.5">
                                            @if($user->isSuperAdmin())
                                                <span class="text-[10px] text-rose-600 font-black bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100 inline-block w-fit">سوپر ادمین</span>
                                            @endif
                                            @if($user->trashed())
                                                <span class="text-[10px] text-slate-600 font-black bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200 inline-block w-fit italic">حذف شده</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <i class="ti ti-phone text-blue-500 text-sm"></i>
                                        <span class="font-mono text-sm tracking-wide">{{ $user->phone }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-400">
                                        <i class="ti ti-mail text-slate-400 text-sm"></i>
                                        <span class="text-xs">{{ $user->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-user-role-badges :user="$user" />
                            </td>
                            <td class="text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-slate-700 font-bold tracking-tight">
                                        {{ \Morilog\Jalali\Jalalian::fromDateTime($user->created_at)->format('Y/m/d') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    @if($user->trashed())
                                        <form action="{{ route($routePrefix . 'users.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all duration-300 shadow-sm border border-green-100"
                                                    title="بازیابی کاربر">
                                                <i class="ti ti-rotate-clockwise text-lg"></i>
                                            </button>
                                        </form>

                                        <button type="button" 
                                                class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all duration-300 shadow-sm border border-rose-100 force-delete-btn"
                                                data-url="{{ route($routePrefix . 'users.force-delete', $user->id) }}"
                                                data-name="کاربر «{{ $user->name }}»"
                                                title="حذف دائمی">
                                            <i class="ti ti-trash-x text-lg"></i>
                                        </button>
                                    @else
                                        <a href="{{ route($routePrefix . 'users.show', $user) }}" 
                                        class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm border border-blue-100"
                                        title="مشاهده جزئیات">
                                            <i class="ti ti-eye text-lg"></i>
                                        </a>
                                        <a href="{{ route($routePrefix . 'users.edit', $user) }}" 
                                        class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition-all duration-300 shadow-sm border border-amber-100"
                                        title="ویرایش کاربر">
                                            <i class="ti ti-edit text-lg"></i>
                                        </a>
                                        @if(!$user->isSuperAdmin() && $user->id !== \Illuminate\Support\Facades\Auth::id())
                                            @php
                                                $destroyRouteName = $routePrefix . 'users.destroy';
                                                $destroyRouteExists = Route::has($destroyRouteName);
                                                $destroyUrl = $destroyRouteExists ? route($destroyRouteName, $user) : '#';
                                            @endphp
                                            <button type="button" 
                                                    class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all duration-300 shadow-sm border border-rose-100 delete-btn {{ !$destroyRouteExists ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    data-url="{{ $destroyUrl }}"
                                                    data-name="کاربر «{{ $user->name }}»"
                                                    {{ !$destroyRouteExists ? 'disabled' : '' }}
                                                    title="انتقال به سطل زباله">
                                                <i class="ti ti-trash text-lg"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="ti ti-users text-4xl opacity-20"></i>
                                    </div>
                                    <p class="text-lg font-medium">هیچ کاربری در سیستم ثبت نشده است</p>
                                    <a href="{{ route($routePrefix . 'users.create') }}" class="btn-modern btn-modern-primary mt-6">
                                        <i class="ti ti-plus"></i>
                                        <span>ایجاد اولین کاربر</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-enhanced-table>
        </div>

        @if($users->hasPages())
            <div class="mt-8">
                {{ $users->links() }}
            </div>
        @endif
    </x-enhanced-card>
</div>

<!-- Hidden Forms -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<form id="forceDeleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const forceDeleteBtns = document.querySelectorAll('.force-delete-btn');
    const deleteForm = document.getElementById('deleteForm');
    const forceDeleteForm = document.getElementById('forceDeleteForm');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const name = this.getAttribute('data-name');
            
            if (confirm(`آیا از انتقال ${name} به سطل زباله اطمینان دارید؟`)) {
                deleteForm.action = url;
                deleteForm.submit();
            }
        });
    });

    forceDeleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const name = this.getAttribute('data-name');
            
            if (confirm(`آیا از حذف دائمی ${name} اطمینان دارید؟ این عمل غیرقابل بازگشت است و تمام سوابق کاربر پاک خواهد شد.`)) {
                forceDeleteForm.action = url;
                forceDeleteForm.submit();
            }
        });
    });
});
</script>
@endsection
