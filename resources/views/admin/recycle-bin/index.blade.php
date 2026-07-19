@extends('layouts.admin')

@section('title', 'سطل زباله مرکزی - پارس لیان')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-800">سطل زباله مرکزی</h1>
        <p class="text-slate-500 text-sm mt-1">مدیریت و بازیابی آیتم‌های حذف شده سیستم</p>
    </div>

    <div class="grid grid-cols-1 gap-8">
        @foreach($deletedItems as $type => $items)
        @if($items->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h2 class="font-black text-slate-700 text-sm flex items-center gap-2">
                    {{ \App\Support\RecycleBinRegistry::label($type) }}
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">{{ $items->count() }} مورد</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-white border-b border-slate-50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">عنوان / نام</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">تاریخ حذف</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-left">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700">{{ \App\Support\RecycleBinRegistry::title($type, $item) }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($item->deleted_at)->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.recycle-bin.restore', ['type' => $type, 'id' => $item->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-black hover:bg-emerald-600 hover:text-white transition-all flex items-center gap-2">
                                            <i class="ti ti-rotate-clockwise text-base"></i>
                                            بازیابی
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.recycle-bin.force-delete', ['type' => $type, 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('آیا از حذف دائمی اطمینان دارید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-600 hover:text-white transition-all flex items-center gap-2">
                                            <i class="ti ti-trash-x text-base"></i>
                                            حذف قطعی
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endforeach

        @if(collect($deletedItems)->every(fn($items) => $items->count() === 0))
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="flex flex-col items-center">
                <i class="ti ti-trash-off text-6xl text-slate-100 mb-4"></i>
                <h3 class="text-xl font-black text-slate-300">سطل زباله کاملاً خالی است</h3>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
