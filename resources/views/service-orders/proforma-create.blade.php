@extends('layouts.admin')

@section('title', 'پیش‌فاکتور سفارش ' . hash_ref_plain($serviceOrder->id))

@section('content')
@php
    $customer = $serviceOrder->customer;
    $device = $serviceOrder->device;
    $defaultDescription = trim(sprintf(
        'پیش‌فاکتور سفارش سرویس #%s%s',
        $serviceOrder->id,
        $device ? ' — ' . trim(($device->type ?? '') . ' ' . ($device->model ?? '')) : ''
    ));
    $prefilledItems = $serviceOrder->repairItems;
@endphp
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800">پیش‌فاکتور سفارش <x-hash-ref :value="$serviceOrder->id" /></h1>
            <p class="text-slate-500 text-sm mt-1">اطلاعات سفارش از قبل پر شده؛ در صورت نیاز ویرایش کنید و سپس چاپ بگیرید.</p>
        </div>
        <a href="{{ route('automation.service-orders.show', $serviceOrder) }}" class="btn-modern btn-modern-secondary">بازگشت به سفارش</a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-700 border border-rose-100 font-bold text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-sm">
            <p class="font-black text-slate-700 mb-2">اطلاعات ثابت سفارش</p>
            <p><span class="text-slate-500">مشتری:</span> {{ $customer->name ?? '—' }}</p>
            <p><span class="text-slate-500">دستگاه:</span> {{ $device->type ?? '—' }} — {{ $device->model ?? '—' }}</p>
        </div>
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-sm">
            <p class="font-black text-slate-700 mb-2">راهنما</p>
            <p class="text-slate-600 leading-relaxed">فیلدهای زیر قابل ویرایش هستند. پس از تأیید، پیش‌فاکتور در صفحه چاپ باز می‌شود.</p>
        </div>
    </div>

    <form action="{{ route('automation.repairs.proforma.print', $serviceOrder) }}" method="POST" class="space-y-8" id="proforma-form">
        @csrf
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
            <h2 class="font-black text-slate-800">اطلاعات مشتری (قابل ویرایش)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="customer_name" value="{{ old('customer_name', $customer->name ?? '') }}" placeholder="نام مشتری" class="form-control w-full">
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $customer->phone ?? '') }}" placeholder="تلفن" class="form-control w-full">
                <textarea name="customer_address" rows="2" placeholder="آدرس" class="form-control w-full md:col-span-2">{{ old('customer_address', $customer->address ?? '') }}</textarea>
                <textarea name="description" rows="2" placeholder="توضیحات پیش‌فاکتور" class="form-control w-full md:col-span-2">{{ old('description', $defaultDescription) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-black text-slate-800">اقلام (از سفارش — قابل ویرایش)</h2>
                <button type="button" onclick="addProformaRow()" class="text-blue-600 font-bold text-sm flex items-center gap-1">
                    <i class="ti ti-plus"></i> افزودن ردیف
                </button>
            </div>
            <div id="proforma-items" class="space-y-3">
                @forelse(old('items', $prefilledItems) as $index => $item)
                    @php
                        $title = is_array($item) ? ($item['title'] ?? '') : ($item->name ?? '');
                        $quantity = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                        $unitPrice = is_array($item)
                            ? ($item['unit_price'] ?? '')
                            : \App\Support\ShopFormat::moneyAmount($item->cost ?? 0);
                    @endphp
                    <div class="grid grid-cols-12 gap-3 proforma-row items-end">
                        <div class="col-span-5"><input type="text" name="items[{{ $index }}][title]" value="{{ $title }}" placeholder="شرح کالا/خدمت" class="form-control w-full" required></div>
                        <div class="col-span-2"><input type="number" name="items[{{ $index }}][quantity]" value="{{ $quantity }}" min="1" class="form-control w-full" required></div>
                        <div class="col-span-3">
                            <input type="text" name="items[{{ $index }}][unit_price]" value="{{ $unitPrice }}" placeholder="قیمت واحد (تومان)" class="form-control w-full" required
                                   data-money-input data-money-words="#money-words-{{ $index }}" data-money-words-url="{{ route('automation.money.words') }}" inputmode="numeric" autocomplete="off">
                            <p id="money-words-{{ $index }}" class="text-[10px] font-bold text-slate-500 mt-1 min-h-[1rem]"></p>
                        </div>
                        <div class="col-span-2"><button type="button" onclick="removeProformaRow(this)" class="w-full py-2 text-rose-500 bg-rose-50 rounded-xl text-sm">حذف</button></div>
                    </div>
                @empty
                    <div class="grid grid-cols-12 gap-3 proforma-row items-end">
                        <div class="col-span-5"><input type="text" name="items[0][title]" placeholder="شرح کالا/خدمت" class="form-control w-full" required></div>
                        <div class="col-span-2"><input type="number" name="items[0][quantity]" value="1" min="1" class="form-control w-full" required></div>
                        <div class="col-span-3">
                            <input type="text" name="items[0][unit_price]" placeholder="قیمت واحد (تومان)" class="form-control w-full" required
                                   data-money-input data-money-words="#money-words-0" data-money-words-url="{{ route('automation.money.words') }}" inputmode="numeric" autocomplete="off">
                            <p id="money-words-0" class="text-[10px] font-bold text-slate-500 mt-1 min-h-[1rem]"></p>
                        </div>
                        <div class="col-span-2"><button type="button" onclick="removeProformaRow(this)" class="w-full py-2 text-rose-500 bg-rose-50 rounded-xl text-sm">حذف</button></div>
                    </div>
                @endforelse
            </div>
        </div>

        <button type="submit" class="btn-modern btn-modern-primary px-8 py-4">
            <i class="ti ti-printer"></i>
            ساخت و چاپ پیش‌فاکتور
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let proformaIndex = {{ max($prefilledItems->count(), count(old('items', [])), 1) }};

    function proformaPriceFieldHtml(index) {
        return `
            <div class="col-span-3">
                <input type="text" name="items[${index}][unit_price]" placeholder="قیمت واحد (تومان)" class="form-control w-full" required
                       data-money-input data-money-words="#money-words-${index}" data-money-words-url="{{ route('automation.money.words') }}" inputmode="numeric" autocomplete="off">
                <p id="money-words-${index}" class="text-[10px] font-bold text-slate-500 mt-1 min-h-[1rem]"></p>
            </div>`;
    }

    function addProformaRow() {
        const container = document.getElementById('proforma-items');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-3 proforma-row items-end';
        row.innerHTML = `
            <div class="col-span-5"><input type="text" name="items[${proformaIndex}][title]" placeholder="شرح کالا/خدمت" class="form-control w-full" required></div>
            <div class="col-span-2"><input type="number" name="items[${proformaIndex}][quantity]" value="1" min="1" class="form-control w-full" required></div>
            ${proformaPriceFieldHtml(proformaIndex)}
            <div class="col-span-2"><button type="button" onclick="removeProformaRow(this)" class="w-full py-2 text-rose-500 bg-rose-50 rounded-xl text-sm">حذف</button></div>
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
