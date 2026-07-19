@extends('layouts.admin')

@section('title', 'صدور پیش‌فاکتور - پارس لیان')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800">صدور پیش‌فاکتور</h1>
            <p class="text-slate-500 text-sm mt-1">اطلاعات را وارد کنید و پیش‌فاکتور را چاپ یا PDF بگیرید.</p>
        </div>
        <a href="{{ route('automation.accounting.index') }}" class="btn-modern btn-modern-secondary">بازگشت</a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-700 border border-rose-100 font-bold text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('automation.accounting.proforma.print') }}" method="POST" class="space-y-8" id="proforma-form">
        @csrf
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
            <h2 class="font-black text-slate-800">اطلاعات مشتری</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="customer_name" placeholder="نام مشتری" class="form-control w-full">
                <input type="text" name="customer_phone" placeholder="تلفن" class="form-control w-full">
                <textarea name="customer_address" rows="2" placeholder="آدرس" class="form-control w-full md:col-span-2"></textarea>
                <textarea name="description" rows="2" placeholder="توضیحات پیش‌فاکتور" class="form-control w-full md:col-span-2"></textarea>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-black text-slate-800">اقلام</h2>
                <button type="button" onclick="addProformaRow()" class="text-blue-600 font-bold text-sm flex items-center gap-1">
                    <i class="ti ti-plus"></i> افزودن ردیف
                </button>
            </div>
            <div class="hidden md:grid proforma-row-head gap-3 text-xs font-black text-slate-500 mb-2 px-1">
                <span>شرح کالا/خدمت</span>
                <span>تعداد</span>
                <span>قیمت واحد (تومان)</span>
                <span></span>
            </div>
            <div id="proforma-items" class="space-y-3">
                <div class="proforma-row">
                    <div class="proforma-field proforma-field-title">
                        <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">شرح</label>
                        <input type="text" name="items[0][title]" placeholder="شرح کالا/خدمت" class="form-control w-full" required>
                    </div>
                    <div class="proforma-field proforma-field-qty">
                        <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">تعداد</label>
                        <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control w-full" required>
                    </div>
                    <div class="proforma-field proforma-field-price">
                        <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">قیمت واحد</label>
                        <input type="text" name="items[0][unit_price]" placeholder="قیمت واحد (تومان)" class="form-control w-full" required
                               data-money-input data-money-words="#money-words-0" data-money-words-url="{{ route('automation.money.words') }}" inputmode="numeric" autocomplete="off">
                        <p id="money-words-0" class="text-[10px] font-bold text-slate-500 mt-1 min-h-[1rem]"></p>
                    </div>
                    <div class="proforma-field proforma-field-action">
                        <button type="button" onclick="removeProformaRow(this)" class="w-full py-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl text-sm font-bold">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-modern btn-modern-primary px-8 py-4">
            <i class="ti ti-printer"></i>
            ساخت و چاپ پیش‌فاکتور
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .proforma-row-head,
    .proforma-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 96px minmax(150px, 200px) 88px;
        gap: 0.75rem;
        align-items: start;
    }

    @media (max-width: 767px) {
        .proforma-row-head { display: none; }
        .proforma-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let proformaIndex = 1;

    function proformaPriceFieldHtml(index) {
        return `
            <div class="proforma-field proforma-field-price">
                <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">قیمت واحد</label>
                <input type="text" name="items[${index}][unit_price]" placeholder="قیمت واحد (تومان)" class="form-control w-full" required
                       data-money-input data-money-words="#money-words-${index}" data-money-words-url="{{ route('automation.money.words') }}" inputmode="numeric" autocomplete="off">
                <p id="money-words-${index}" class="text-[10px] font-bold text-slate-500 mt-1 min-h-[1rem]"></p>
            </div>`;
    }

    function addProformaRow() {
        const container = document.getElementById('proforma-items');
        const row = document.createElement('div');
        row.className = 'proforma-row';
        row.innerHTML = `
            <div class="proforma-field proforma-field-title">
                <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">شرح</label>
                <input type="text" name="items[${proformaIndex}][title]" placeholder="شرح کالا/خدمت" class="form-control w-full" required>
            </div>
            <div class="proforma-field proforma-field-qty">
                <label class="md:hidden text-xs font-bold text-slate-500 mb-1 block">تعداد</label>
                <input type="number" name="items[${proformaIndex}][quantity]" value="1" min="1" class="form-control w-full" required>
            </div>
            ${proformaPriceFieldHtml(proformaIndex)}
            <div class="proforma-field proforma-field-action">
                <button type="button" onclick="removeProformaRow(this)" class="w-full py-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl text-sm font-bold">حذف</button>
            </div>
        `;
        container.appendChild(row);
        if (window.initMoneyInputs) window.initMoneyInputs(row);
        proformaIndex++;
    }

    function removeProformaRow(btn) {
        const rows = document.querySelectorAll('.proforma-row');
        if (rows.length <= 1) return;
        btn.closest('.proforma-row').remove();
    }

    document.getElementById('proforma-form')?.addEventListener('submit', function () {
        document.querySelectorAll('[data-money-input]').forEach((input) => {
            const raw = input.dataset.rawValue || input.value.replace(/[^\d]/g, '');
            input.value = raw;
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (window.initMoneyInputs) window.initMoneyInputs();
    });
</script>
@endpush
