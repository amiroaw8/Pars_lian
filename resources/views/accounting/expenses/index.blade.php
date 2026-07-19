@extends('layouts.admin')

@section('title', 'مدیریت هزینه‌ها - پارس لیان')

@section('content')
<div class="relative pb-12">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-[30rem] h-[30rem] bg-rose-500/10 rounded-full blur-[100px] animate-blob"></div>
        <div class="absolute bottom-0 right-1/4 w-[20rem] h-[20rem] bg-amber-500/5 rounded-full blur-[100px] animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-12 relative z-10">
        <x-page-header 
            title="مدیریت هزینه‌های شرکت" 
            subtitle="ثبت و دسته‌بندی تمامی مخارج جاری برای محاسبه دقیق سود و زیان."
            badge="Expenses Management"
            badgeIcon="ti-cash-banknote"
            headerIcon="ti-cash"
            actionUrl="{{ route('automation.accounting.expenses.create') }}"
            actionText="ثبت هزینه جدید"
        />

        <div class="animate-slide-up">
            <x-enhanced-table icon="ti-list-details" animated>
                <x-slot name="headers">
                    <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">عنوان هزینه</th>
                    <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">مبلغ</th>
                    <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">دسته‌بندی</th>
                    <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">تاریخ</th>
                    <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">عملیات</th>
                </x-slot>

                <x-slot name="rows">
                    @forelse($expenses as $expense)
                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all shadow-sm group-hover:rotate-6">
                                    <i class="ti ti-receipt text-xl"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-700 group-hover:text-rose-600 transition-colors">{{ $expense->title }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">By {{ $expense->creator->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-lg font-black text-slate-900 tracking-tight">{{ number_format($expense->amount) }}</span>
                            <span class="text-[10px] text-slate-400 font-bold mr-1">تومان</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-[11px] font-black border border-slate-200/50">
                                <i class="ti ti-category text-xs"></i>
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-600">
                                    @if(class_exists('\Morilog\Jalali\Jalalian'))
                                        {{ \Morilog\Jalali\Jalalian::fromCarbon($expense->expense_date)->format('Y/m/d') }}
                                    @else
                                        {{ $expense->expense_date->format('Y/m/d') }}
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('automation.accounting.expenses.edit', $expense) }}" class="p-2.5 rounded-xl bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white transition-all shadow-sm border border-indigo-100 group/btn">
                                    <i class="ti ti-edit text-lg group-hover/btn:scale-110"></i>
                                </a>
                                <form action="{{ route('automation.accounting.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('آیا از حذف این هزینه اطمینان دارید؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100 group/btn">
                                        <i class="ti ti-trash text-lg group-hover/btn:scale-110"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-6">
                                <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="ti ti-receipt-off text-6xl"></i>
                                </div>
                                <p class="text-slate-400 font-bold">هیچ هزینه‌ای ثبت نشده است.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </x-slot>
            </x-enhanced-table>
        </div>
        {{ $expenses->links() }}
    </div>
</div>
@endsection
