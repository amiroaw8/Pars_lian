@extends('layouts.admin')

@section('title', 'ثبت هزینه جدید - پارس لیان')

@section('content')
<div class="max-w-4xl mx-auto space-y-12 pb-12">
    <x-page-header 
        title="ثبت هزینه جدید" 
        subtitle="مخارج جدید شرکت را با جزئیات دقیق برای شفافیت مالی ثبت کنید."
        badge="New Expense"
        badgeIcon="ti-plus"
        headerIcon="ti-cash-banknote"
    />

    <form action="{{ route('automation.accounting.expenses.store') }}" method="POST" class="animate-slide-up">
        @csrf
        <x-enhanced-card title="جزئیات هزینه" icon="ti-info-circle">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 p-4">
                <div class="form-group-modern group">
                    <label for="title" class="form-label-modern">
                        <i class="ti ti-heading"></i>
                        عنوان هزینه
                    </label>
                    <input type="text" name="title" id="title" class="form-control-modern @error('title') border-rose-500 @enderror" value="{{ old('title') }}" placeholder="مثلاً: اجاره دفتر مهر ماه" required>
                    @error('title') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="form-group-modern group">
                    <label for="amount" class="form-label-modern">
                        <i class="ti ti-coin"></i>
                        مبلغ (تومان)
                    </label>
                    <input type="number" name="amount" id="amount" class="form-control-modern @error('amount') border-rose-500 @enderror" value="{{ old('amount') }}" placeholder="مثلاً: 5000000" min="0" required>
                    @error('amount') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="form-group-modern group">
                    <label for="category" class="form-label-modern">
                        <i class="ti ti-category"></i>
                        دسته‌بندی
                    </label>
                    <select name="category" id="category" class="form-control-modern @error('category') border-rose-500 @enderror" required>
                        <option value="">انتخاب کنید...</option>
                        <option value="اجاره">اجاره</option>
                        <option value="قبوض">قبوض (آب، برق و...)</option>
                        <option value="خرید قطعات">خرید قطعات</option>
                        <option value="حقوق">حقوق و دستمزد</option>
                        <option value="سایر">سایر</option>
                    </select>
                    @error('category') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="form-group-modern group">
                    <label for="expense_date" class="form-label-modern">
                        <i class="ti ti-calendar"></i>
                        تاریخ هزینه
                    </label>
                    <input type="text" name="expense_date" id="expense_date" class="form-control-modern @error('expense_date') border-rose-500 @enderror" value="{{ old('expense_date', \Morilog\Jalali\Jalalian::now()->format('Y/m/d')) }}" placeholder="۱۴۰۴/۰۱/۰۱" dir="ltr" required>
                    @error('expense_date') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="form-group-modern group md:col-span-2">
                    <label for="description" class="form-label-modern">
                        <i class="ti ti-notes"></i>
                        توضیحات تکمیلی
                    </label>
                    <textarea name="description" id="description" rows="4" class="form-control-modern @error('description') border-rose-500 @enderror" placeholder="توضیحات بیشتر در مورد این هزینه...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-12 flex items-center justify-end gap-4 p-4">
                <a href="{{ route('automation.accounting.expenses.index') }}" class="btn-modern btn-modern-secondary">انصراف</a>
                <button type="submit" class="btn-modern btn-modern-primary group shadow-lg shadow-primary-500/20">
                    <i class="ti ti-check group-hover:scale-110 transition-transform"></i>
                    ثبت نهایی هزینه
                </button>
            </div>
        </x-enhanced-card>
    </form>
</div>
@endsection
