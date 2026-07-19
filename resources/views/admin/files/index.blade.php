@extends('layouts.admin')

@section('title', 'مدیریت فایل‌ها')

@section('content')
<div class="page-header animate-slide-up mb-8">
    <div>
        <h1 class="page-title text-gradient">
            <i class="ti ti-files"></i>
            مدیریت فایل‌های سایت
        </h1>
        <div class="breadcrumb text-secondary-600">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">داشبورد</a>
            <i class="ti ti-chevron-left"></i>
            <span>فایل‌ها</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <div class="text-xs text-slate-500 mb-1">کل فایل‌ها</div>
        <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total']) }}</div>
        <div class="text-[10px] text-slate-400 mt-1">پیوست‌ها، تصاویر محصول، لوگو و فایل‌های دیسک</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <div class="text-xs text-slate-500 mb-1">حجم کل</div>
        <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_size'] / 1024 / 1024, 2) }} <span class="text-sm font-bold">MB</span></div>
    </div>
    @foreach($stats['by_category'] as $category => $count)
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-500 mb-1 line-clamp-2">{{ $category }}</div>
            <div class="text-2xl font-black text-slate-800">{{ number_format($count) }}</div>
        </div>
    @endforeach
</div>

@forelse($grouped as $category => $files)
    @php
        $isOrphanSection = $category === \App\Services\SiteFileCatalog::CATEGORY_LABELS['disk_orphan'];
    @endphp
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm mb-6 overflow-hidden {{ $isOrphanSection ? 'ring-1 ring-amber-100' : '' }}">
        <div class="px-6 py-4 border-b border-slate-100 {{ $isOrphanSection ? 'bg-amber-50/60' : 'bg-slate-50' }} flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-800 flex items-center gap-2">
                    <i class="ti {{ $isOrphanSection ? 'ti-alert-triangle text-amber-500' : 'ti-folder text-primary-500' }}"></i>
                    {{ $category }}
                </h3>
                @if($isOrphanSection)
                    <p class="text-xs text-amber-700/80 mt-1 max-w-3xl">این فایل‌ها روی دیسک سرور هستند ولی رکورد دیتابیس ندارند (یا رکوردشان حذف شده). در صورت امکان از لاگ سیستم، سفارش و نام اصلی فایل بازیابی شده است.</p>
                @endif
            </div>
            <span class="text-xs font-bold text-slate-500 shrink-0">{{ $files->count() }} فایل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right min-w-[900px]">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase bg-slate-50/50">
                        <th class="px-4 py-3">نام / نوع</th>
                        <th class="px-4 py-3">مرتبط با</th>
                        <th class="px-4 py-3">محل ذخیره</th>
                        <th class="px-4 py-3">حجم</th>
                        <th class="px-4 py-3">آپلودکننده</th>
                        <th class="px-4 py-3">تاریخ</th>
                        <th class="px-4 py-3">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($files as $file)
                        <tr class="hover:bg-slate-50/50 align-top">
                            <td class="px-4 py-4 min-w-[180px]">
                                <div class="font-bold text-slate-800 break-words">{{ $file->display_name ?? $file->name }}</div>
                                @if(($file->display_name ?? $file->name) !== $file->name)
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono break-all" dir="ltr">{{ $file->name }}</div>
                                @endif
                                <div class="inline-flex items-center gap-1 mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                    @if($file->is_image ?? false)
                                        <i class="ti ti-photo"></i>
                                    @else
                                        <i class="ti ti-file"></i>
                                    @endif
                                    {{ $file->mime_label ?? 'فایل' }}
                                </div>
                                @if(!empty($file->status_note))
                                    <p class="text-[10px] text-amber-700 mt-2 leading-relaxed">{{ $file->status_note }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-600 text-xs min-w-[140px]">
                                @if($file->related_url)
                                    <a href="{{ $file->related_url }}" class="text-primary-600 hover:underline font-bold">
                                        {{ $file->related_label }}
                                    </a>
                                @elseif($file->related_label)
                                    <span>{{ $file->related_label }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs min-w-[130px]">
                                <div class="font-bold text-slate-700">{{ $file->storage_label ?? '—' }}</div>
                                @if(!empty($file->storage_path))
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono break-all" dir="ltr">{{ $file->storage_path }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-600 whitespace-nowrap">{{ $file->human_size }}</td>
                            <td class="px-4 py-4 text-slate-600 text-xs whitespace-nowrap">{{ $file->uploader_name ?? '—' }}</td>
                            <td class="px-4 py-4 text-slate-500 text-xs whitespace-nowrap">
                                @if($file->created_at)
                                    @if(class_exists('\Morilog\Jalali\Jalalian'))
                                        {{ \Morilog\Jalali\Jalalian::fromCarbon($file->created_at)->format('Y/m/d H:i') }}
                                    @else
                                        {{ $file->created_at->format('Y/m/d H:i') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-2">
                                    @if($file->download_url !== '#')
                                        <a href="{{ $file->download_url }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary-600 hover:text-primary-700">
                                            <i class="ti ti-download"></i>
                                            دانلود
                                        </a>
                                    @endif
                                    @if(!empty($file->can_delete) && !empty($file->delete_key))
                                        <form method="POST" action="{{ route('admin.files.destroy') }}" class="inline" onsubmit="return confirm('این فایل به سطل زباله مرکزی منتقل می‌شود. ادامه می‌دهید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="key" value="{{ $file->delete_key }}">
                                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700">
                                                <i class="ti ti-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200">
        <i class="ti ti-file-off text-5xl text-slate-300 mb-4"></i>
        <p class="text-slate-500">هنوز فایلی آپلود نشده است.</p>
    </div>
@endforelse
@endsection
