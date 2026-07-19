@extends('layouts.admin')

@section('styles')
<style>
    .service-order-show-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1.25rem;
    }
    .service-order-show-header .page-header-intro {
        width: 100%;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    .service-order-show-header .page-header-actions {
        width: 100%;
        position: relative;
        z-index: 2;
        justify-content: flex-start;
        row-gap: 0.75rem;
    }
    .service-order-show-header .page-title {
        flex-wrap: wrap;
    }
    .service-print-dropdown {
        position: absolute;
        bottom: calc(100% + 8px);
        top: auto;
        right: 0;
        left: auto;
        z-index: 10060;
        min-width: 220px;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 -8px 30px rgba(15, 23, 42, 0.12);
        padding: 0.5rem 0;
        pointer-events: auto;
    }
    .service-print-dropdown.hidden {
        display: none !important;
    }
    .service-print-dropdown.is-open {
        z-index: 10060;
    }
    .print-menu-host.is-menu-open {
        z-index: 10060;
    }
    .service-print-dropdown button {
        display: block;
        width: 100%;
        text-align: right;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .service-print-dropdown button:hover {
        background: #f8fafc;
    }
    .page-title-heading {
        background: var(--bg-gradient-primary, linear-gradient(135deg, #2563eb, #7c3aed));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .service-order-show-header h1 .inline-flex {
        -webkit-text-fill-color: initial;
        background-image: none;
        background-clip: padding-box;
        -webkit-background-clip: padding-box;
    }
    .repair-item-add-form {
        padding: 1.5rem;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        border: 1px solid #e2e8f0;
    }
    .repair-item-add-form .form-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .repair-item-add-form .form-control {
        font-size: 0.9375rem;
        padding: 0.75rem 1rem;
        min-height: 2.75rem;
    }
    .repair-item-add-form .repair-item-fields-row .form-label {
        font-size: 0.9375rem;
        margin-bottom: 0.625rem;
    }
    .repair-item-add-form .repair-item-fields-row {
        display: grid;
        grid-template-columns: minmax(5.5rem, 0.55fr) minmax(10rem, 1.2fr) minmax(12rem, 2fr);
        gap: 1rem;
        width: 100%;
    }
    .repair-item-add-form .repair-item-fields-row > div {
        min-width: 0;
        width: 100%;
    }
    .repair-item-add-form .repair-item-fields-row .form-control {
        font-size: 1.0625rem;
        padding: 0.9375rem 1.125rem;
        min-height: 3.5rem;
        width: 100%;
        box-sizing: border-box;
    }
    .repair-item-type-toggle span {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
    .repair-item-add-form .select2-container--default .select2-selection--single {
        min-height: 2.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.375rem 0.75rem;
        background: #fff;
    }
    .repair-item-add-form .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
        padding-right: 0.25rem;
        color: #334155;
        font-size: 0.9375rem;
    }
    .repair-item-add-form .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.5rem;
    }
    .repair-item-add-form .select2-container {
        width: 100% !important;
    }
    .attachment-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 1.5rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .attachment-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08);
    }
    .attachment-card-preview {
        position: relative;
        aspect-ratio: 16 / 10;
        background: #f8fafc;
        overflow: hidden;
    }
    .attachment-card-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .attachment-card:hover .attachment-card-preview img {
        transform: scale(1.04);
    }
    .attachment-card-preview-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0);
        transition: background 0.3s;
    }
    .attachment-card:hover .attachment-card-preview-overlay {
        background: rgba(15, 23, 42, 0.25);
    }
    .attachment-card-preview-overlay i {
        font-size: 2rem;
        color: #fff;
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.3s, transform 0.3s;
    }
    .attachment-card:hover .attachment-card-preview-overlay i {
        opacity: 1;
        transform: scale(1);
    }
    .attachment-card-file-icon {
        aspect-ratio: 16 / 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
    .attachment-upload-zone {
        padding: 1.25rem 1.5rem;
        border-radius: 1.25rem;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
    }
    .attachment-upload-zone .form-control {
        font-size: 0.9375rem;
        padding: 0.75rem 1rem;
    }
    .debt-record-fields {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    @media (min-width: 1024px) {
        .debt-record-fields {
            flex-direction: row;
            align-items: flex-start;
        }
        .debt-record-fields .debt-record-amount {
            flex: 0 0 38%;
            max-width: 38%;
        }
        .debt-record-fields .debt-record-reason {
            flex: 1 1 auto;
            min-width: 0;
        }
    }
</style>
@endsection

@section('title', 'service - ' . $serviceOrder->id)

@section('content')
@php
    $linkedShopOrder = $serviceOrder->shopOrders()->latest()->first();
@endphp
<div class="screen-only">
<div class="page-header service-order-show-header overflow-visible relative" id="order-actions-section" data-scroll-section="order-actions-section">
    <div class="page-header-intro">
        <h1 class="flex flex-wrap items-center gap-3 mb-4 text-2xl md:text-3xl font-bold">
            <i class="ti ti-clipboard-list text-primary-600"></i>
            <span class="page-title-heading">مشاهده سفارش سرویس</span>
            <x-hash-ref :value="$serviceOrder->id" class="text-2xl md:text-3xl font-bold text-slate-800 align-middle" />
            <x-enhanced-status-badge :status="$serviceOrder->status?->value ?? $serviceOrder->status ?? 'registered'" class="shrink-0 mr-1 align-middle" />
        </h1>
        <div class="breadcrumb">
            <a href="/" class="breadcrumb-item">
                <i class="ti ti-home"></i>
                خانه
            </a>
            <i class="ti ti-chevron-left breadcrumb-separator"></i>
            <a href="{{ route('automation.service-orders.index') }}" class="breadcrumb-item">
                سفارشات سرویس
            </a>
            <i class="ti ti-chevron-left breadcrumb-separator"></i>
            <span class="breadcrumb-item active">سفارش <x-hash-ref :value="$serviceOrder->id" /></span>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 page-header-actions">
        <!-- New Actions -->
        @if(auth()->user()->isTechnician() && !$serviceOrder->technician_id && $serviceOrder->status === \App\Enums\ServiceOrderStatus::REGISTERED)
            <form action="{{ route('automation.service-orders.assign-self', $serviceOrder) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-modern btn-modern-warning">
                    <i class="ti ti-hand-stop"></i>
                    <span>تخصیص به من</span>
                </button>
            </form>
        @endif

        @if(!$serviceOrder->technician_id && $serviceOrder->status === \App\Enums\ServiceOrderStatus::REGISTERED && (auth()->user()->isAdmin() || auth()->user()->isReceptionist()))
            <form action="{{ route('automation.service-orders.assign-technician', $serviceOrder) }}" method="POST" class="inline flex items-center gap-2">
                @csrf
                <select name="technician_id" class="form-control text-sm h-10 min-w-[160px]" required>
                    <option value="">انتخاب تکنسین...</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-modern btn-modern-warning">
                    <i class="ti ti-user-check"></i>
                    <span>تخصیص تکنسین</span>
                </button>
            </form>
        @endif

        @php
            $isAssignedToMe = auth()->user()->isTechnician() && (int) $serviceOrder->technician_id === (int) auth()->id();
            $canStartRepairStatus = in_array($serviceOrder->status, [
                \App\Enums\ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                \App\Enums\ServiceOrderStatus::REGISTERED,
            ], true) && $serviceOrder->technician_id;
        @endphp

        @if($canStartRepairStatus && $serviceOrder->technician_id && auth()->user()->canManageRepairs())
            @php
                $canStartRepair = auth()->user()->isAdmin() || auth()->user()->isReceptionist() || $isAssignedToMe;
            @endphp
            @if($canStartRepair)
            <form action="{{ route('automation.repairs.start', $serviceOrder) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-modern btn-modern-success">
                    <i class="ti ti-player-play"></i>
                    <span>شروع تعمیر</span>
                </button>
            </form>
            @endif
        @endif

        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::REPAIRING && auth()->user()->canManageRepairs())
            <form action="{{ route('automation.repairs.complete', $serviceOrder) }}" method="POST" class="inline" onsubmit="return confirm('آیا از اتمام تعمیر اطمینان دارید؟')">
                @csrf
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="ti ti-check"></i>
                    <span>اتمام تعمیر</span>
                </button>
            </form>
        @endif

        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::ACCOUNTING && (auth()->user()->canManageAccounting() || auth()->user()->isAdmin()))
            <form action="{{ route('automation.repairs.verify-payment', $serviceOrder) }}" method="POST" class="inline" onsubmit="return confirm('آیا از تایید پرداخت و دریافت مبلغ اطمینان دارید؟')">
                @csrf
                <button type="submit" class="btn-modern btn-modern-success">
                    <i class="ti ti-cash"></i>
                    <span>تایید پرداخت</span>
                </button>
            </form>

            <form action="{{ route('automation.repairs.record-debt', $serviceOrder) }}" method="POST" class="inline" onsubmit="return confirm('مبلغ بدهی برابر با جمع نهایی تعمیر ({{ number_format($serviceOrder->service_cost) }} تومان) ثبت شود؟')">
                @csrf
                <input type="hidden" name="debt_reason" value="بدهی مشتری — عدم پرداخت پس از اتمام تعمیر">
                <button type="submit" class="btn-modern btn-modern-warning">
                    <i class="ti ti-receipt-tax"></i>
                    <span>ثبت بدهکاری</span>
                </button>
            </form>
            
            <button type="button" onclick="openRejectModal()" class="btn-modern btn-modern-danger">
                <i class="ti ti-x"></i>
                <span>رد سفارش</span>
            </button>
        @endif

        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::READY && auth()->user()->canEditServiceOrders())
            <form method="POST" action="{{ route('automation.repairs.deliver', $serviceOrder) }}" onsubmit="return confirm('آیا از تحویل دستگاه اطمینان دارید؟')">
                @csrf
                <button type="submit" class="btn-modern btn-modern-success">
                    <i class="ti ti-check-double"></i>
                    <span>تحویل دستگاه</span>
                </button>
            </form>
        @endif

        @if($serviceOrder->customer && (auth()->user()->canManageAccounting() || auth()->user()->canManageCustomers() || auth()->user()->isAdmin()))
            <a
                href="{{ route('automation.customers.show', $serviceOrder->customer) }}#customer-financial-section"
                class="btn-modern btn-modern-success flex items-center gap-2 px-6 py-2"
                title="پرداختی‌ها، بدهی‌ها و تراکنش‌های {{ $serviceOrder->customer->name }}"
            >
                <i class="ti ti-wallet text-lg"></i>
                <span class="font-bold">مدیریت مالی مشتری</span>
            </a>
        @endif

        <a href="{{ route('automation.service-orders.index') }}" class="btn-modern btn-modern-secondary">
            <i class="ti ti-arrow-right"></i>
            بازگشت به لیست
        </a>
        @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting() || auth()->user()->canAccessAdminPanel() || (auth()->user()->hasRole('customer') && $serviceOrder->customer_id === auth()->user()->customer?->id))
            <a href="{{ route('automation.repairs.export-actions', $serviceOrder) }}" class="btn-modern btn-modern-neutral flex items-center gap-2 px-6 py-2">
                <i class="ti ti-download text-lg"></i>
                <span class="font-bold">دانلود لیست اقدامات</span>
            </a>
        @endif
        <div class="relative inline-block print-menu-host">
            <button type="button" data-print-menu-toggle="receipt-menu" class="btn-modern btn-modern-primary flex items-center gap-2 px-6 py-2 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all duration-300 group">
                <i class="ti ti-printer text-lg group-hover:scale-110 transition-transform duration-300"></i>
                <span class="font-bold">چاپ رسیدها</span>
                <i class="ti ti-chevron-down text-sm"></i>
            </button>
            <div id="receipt-menu" data-print-menu data-min-width="240" class="service-print-dropdown hidden">
                <button type="button" onclick="openReceiptPrint('full')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">رسید کل</button>
                <button type="button" onclick="openReceiptPrint('receipt')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">رسید دریافت توسط شرکت</button>
                <button type="button" onclick="openReceiptPrint('delivery')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">رسید تحویل دستگاه</button>
                <button type="button" onclick="openReceiptPrint('mini')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">مینی رسید</button>
            </div>
        </div>
        @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting() || auth()->user()->canAccessAdminPanel() || (auth()->user()->hasRole('customer') && $serviceOrder->customer_id === auth()->user()->customer?->id))
            <div class="relative inline-block print-menu-host">
                <button type="button" data-print-menu-toggle="invoice-menu" class="btn-modern btn-modern-neutral flex items-center gap-2 px-6 py-2">
                    <i class="ti ti-file-invoice text-lg"></i>
                    <span class="font-bold">چاپ فاکتور</span>
                    <i class="ti ti-chevron-down text-sm"></i>
                </button>
                <div id="invoice-menu" data-print-menu data-min-width="220" class="service-print-dropdown hidden">
                    <button type="button" onclick="openInvoicePrint('invoice')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">فاکتور خدمات (تعمیر)</button>
                    <button type="button" onclick="openInvoicePrint('sale')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">فاکتور فروش</button>
                    <button type="button" onclick="openInvoicePrint('proforma')" class="w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">پیش‌فاکتور</button>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/Sortable.min.js') }}"></script>
<script>
    const printSheetUrl = @json(route('automation.repairs.print-sheet', $serviceOrder));
    const proformaFormUrl = @json(route('automation.repairs.proforma.create', $serviceOrder));
    const linkedShopOrderPrintUrl = @json($linkedShopOrder ? route('automation.orders.print', ['order' => $linkedShopOrder, 'type' => 'invoice']) : null);

    function openPrintPage(url) {
        const separator = url.includes('?') ? '&' : '?';
        window.open(url + separator + 'auto_print=1', '_blank', 'noopener');
    }

    function closePrintMenus() {
        document.querySelectorAll('[data-print-menu]').forEach(function (menu) {
            menu.classList.add('hidden');
            menu.classList.remove('is-open');
        });
        document.querySelectorAll('.print-menu-host.is-menu-open').forEach(function (host) {
            host.classList.remove('is-menu-open');
        });
    }

    function openPrintMenu(menu, button) {
        menu.classList.remove('hidden');
        menu.classList.add('is-open');
        button.closest('.print-menu-host')?.classList.add('is-menu-open');
    }

    function openReceiptPrint(layout) {
        closePrintMenus();
        openPrintPage(printSheetUrl + '?layout=' + encodeURIComponent(layout));
    }

    function openInvoicePrint(type) {
        closePrintMenus();
        if (type === 'proforma') {
            window.location.href = proformaFormUrl;
            return;
        }
        if (type === 'sale' && linkedShopOrderPrintUrl) {
            openPrintPage(linkedShopOrderPrintUrl);
            return;
        }
        openPrintPage(printSheetUrl + '?layout=' + encodeURIComponent(type));
    }

    function initPrintDropdownMenus() {
        document.querySelectorAll('[data-print-menu-toggle]').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                const menuId = button.getAttribute('data-print-menu-toggle');
                const menu = document.getElementById(menuId);
                if (!menu) return;

                const isOpen = !menu.classList.contains('hidden');
                closePrintMenus();

                if (!isOpen) {
                    openPrintMenu(menu, button);
                }
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-print-menu]') && !e.target.closest('[data-print-menu-toggle]')) {
                closePrintMenus();
            }
        });

        document.querySelector('.admin-main')?.addEventListener('scroll', closePrintMenus, { passive: true });
    }

    function toggleRepairItemEdit(id) {
        document.getElementById('repair-item-edit-' + id)?.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        initPrintDropdownMenus();
        document.querySelectorAll('form.inline').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if(btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    const icon = btn.querySelector('i');
                    const span = btn.querySelector('span');
                    
                    if(icon) {
                        icon.className = 'ti ti-loader animate-spin';
                    }
                    if(span) {
                        span.textContent = 'در حال پردازش...';
                    }
                }
            });
        });

        initRepairItemsSortable();
        initRepairInventorySelect();
        if (window.MoneyInput && typeof window.MoneyInput.bind === 'function') {
            const costInput = document.getElementById('repair-cost-input');
            if (costInput) {
                window.MoneyInput.bind(costInput);
            }
        }
    });

    function initRepairInventorySelect() {
        const select = document.getElementById('repair-inventory-select');
        if (!select || typeof jQuery === 'undefined' || !jQuery.fn.select2) {
            return;
        }

        const $select = jQuery(select);
        if ($select.data('select2')) {
            $select.select2('destroy');
        }

        $select.select2({
            dir: 'rtl',
            language: 'fa',
            width: '100%',
            placeholder: 'جستجو و انتخاب قطعه...',
            allowClear: true,
            dropdownParent: jQuery(document.body),
        });

        $select.on('change', function () {
            updatePartPrice(this);
        });
    }

    function toggleItemType(type) {
        const inventoryContainer = document.getElementById('inventory-select-container');
        const serviceContainer = document.getElementById('service-name-container');
        const otherContainer = document.getElementById('other-name-container');
        const inventorySelect = inventoryContainer?.querySelector('select[name="inventory_id"]');
        const serviceNameInput = document.getElementById('repair-service-name');
        const otherNameInput = document.getElementById('repair-other-name');

        [inventoryContainer, serviceContainer, otherContainer].forEach(el => el?.classList.add('hidden'));
        if (inventorySelect) inventorySelect.removeAttribute('required');
        [serviceNameInput, otherNameInput].forEach(el => {
            if (el) {
                el.removeAttribute('required');
                el.removeAttribute('name');
            }
        });

        if (type === 'part') {
            inventoryContainer?.classList.remove('hidden');
            if (inventorySelect) inventorySelect.setAttribute('required', 'required');
        } else if (type === 'service') {
            serviceContainer?.classList.remove('hidden');
            if (serviceNameInput) {
                serviceNameInput.setAttribute('name', 'name');
                serviceNameInput.setAttribute('required', 'required');
            }
        } else if (type === 'other') {
            otherContainer?.classList.remove('hidden');
            if (otherNameInput) {
                otherNameInput.setAttribute('name', 'name');
                otherNameInput.setAttribute('required', 'required');
            }
        }
    }

    function updatePartPrice(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const price = option?.dataset?.price;
        const name = option?.dataset?.name;
        const costInput = document.querySelector('form[action*="add-item"] input[name="cost"]');
        const serviceNameInput = document.getElementById('repair-service-name');
        if (costInput && price !== undefined && price !== '') {
            const amount = Math.round(parseFloat(price)) || 0;
            costInput.dataset.rawValue = String(amount);
            costInput.value = amount > 0 ? amount.toLocaleString('en-US') : '';
            costInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (name && option.value) {
            let hiddenName = document.getElementById('repair-part-hidden-name');
            if (!hiddenName) {
                hiddenName = document.createElement('input');
                hiddenName.type = 'hidden';
                hiddenName.name = 'name';
                hiddenName.id = 'repair-part-hidden-name';
                selectEl.form?.appendChild(hiddenName);
            }
            hiddenName.value = name;
            if (serviceNameInput) {
                serviceNameInput.removeAttribute('name');
            }
        }
    }

    function initRepairItemsSortable() {
        const tbody = document.getElementById('repair-items-sortable');
        if (!tbody || typeof Sortable === 'undefined') {
            return;
        }

        Sortable.create(tbody, {
            handle: '.repair-drag-handle',
            animation: 150,
            onEnd: function() {
                const rows = tbody.querySelectorAll('.repair-item-row');
                const itemIds = Array.from(rows).map(row => row.dataset.itemId);
                rows.forEach((row, index) => {
                    const numEl = row.querySelector('.repair-item-row-num');
                    if (numEl) {
                        numEl.textContent = String(index + 1);
                    }
                });

                fetch('{{ route('automation.repairs.reorder-items', $serviceOrder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ item_ids: itemIds }),
                }).catch(() => {});
            },
        });
    }
</script>
@endpush

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in" style="animation-delay: 0.1s;">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Quick Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card-modern group p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-600 group-hover:text-white transition-all duration-300">
                        <i class="ti ti-user text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs text-slate-500 mb-1 font-medium">مشتری</div>
                        <div class="text-sm font-bold text-slate-700 truncate">{{ $serviceOrder->customer->name }}</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card-modern group p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="ti ti-device-laptop text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs text-slate-500 mb-1 font-medium">دستگاه</div>
                        <div class="text-sm font-bold text-slate-700 truncate">{{ $serviceOrder->device->model }}</div>
                    </div>
                </div>
            </div>

            <div class="stat-card-modern group p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                        <i class="ti ti-coin text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs text-slate-500 mb-1 font-medium">هزینه نهایی</div>
                        <div class="text-sm font-black text-slate-700 truncate">{{ number_format($serviceOrder->service_cost) }} <span class="text-[10px] font-normal text-slate-400 mr-1">تومان</span></div>
                    </div>
                </div>
            </div>
        </div>

        <x-enhanced-card title="جزئیات کامل سفارش" icon="ti-info-circle">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Customer Info -->
                <div class="info-group-modern">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="ti ti-user-circle text-xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">اطلاعات مشتری و رابط</h3>
                    </div>
                    <div class="space-y-4 pr-3 border-r-2 border-slate-50">
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">نام مشتری:</span>
                            <span class="text-sm font-bold text-slate-700">{{ $serviceOrder->customer->name }}</span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">شماره تماس:</span>
                            <span class="text-sm font-bold text-slate-700 ltr">{{ $serviceOrder->customer->phone }}</span>
                        </div>
                        @if($serviceOrder->receiver_name)
                        <div class="pt-2 mt-2 border-t border-slate-50">
                            <div class="flex justify-between items-center group mb-2">
                                <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">نام تحویل دهنده:</span>
                                <span class="text-sm font-bold text-primary-600">{{ $serviceOrder->receiver_name }}</span>
                            </div>
                            @if($serviceOrder->receiver_phone)
                            <div class="flex justify-between items-center group">
                                <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">تلفن تحویل دهنده:</span>
                                <span class="text-sm font-bold text-slate-700 ltr">{{ $serviceOrder->receiver_phone }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Device Info -->
                <div class="info-group-modern">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="ti ti-device-laptop text-xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">مشخصات دستگاه</h3>
                    </div>
                    <div class="space-y-4 pr-3 border-r-2 border-slate-50">
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">نوع دستگاه:</span>
                            <span class="text-sm font-bold text-slate-700">{{ $serviceOrder->device->type }}</span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">برند و مدل:</span>
                            <span class="text-sm font-bold text-slate-700">{{ $serviceOrder->device->model }}</span>
                        </div>
                        @if($serviceOrder->device->asset_number)
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">شماره اموال:</span>
                            <span class="text-sm font-mono font-bold text-slate-700">{{ $serviceOrder->device->asset_number }}</span>
                        </div>
                        @endif
                        @if($serviceOrder->user_department)
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">واحد بهره‌بردار:</span>
                            <span class="text-sm font-bold text-amber-600">{{ $serviceOrder->user_department }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Service Details -->
                <div class="info-group-modern md:col-span-2">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="ti ti-tool text-xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">جزئیات و شرایط سرویس</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pr-3 border-r-2 border-slate-50">
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">نوع سرویس:</span>
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-xl text-xs font-bold {{ $serviceOrder->service_type == 'in_company' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                                <i class="ti {{ $serviceOrder->service_type == 'in_company' ? 'ti-building' : 'ti-map-pin' }}"></i>
                                {{ $serviceOrder->service_type == 'in_company' ? 'در شرکت' : 'در محل' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">تکنسین مسئول:</span>
                            <span class="text-sm font-bold text-slate-700">{{ $serviceOrder->technician ? $serviceOrder->technician->name : 'تعیین نشده' }}</span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">هزینه کل:</span>
                            <span class="text-sm font-black text-primary-600">{{ number_format($serviceOrder->service_cost) }} تومان</span>
                        </div>
                        @if($serviceOrder->is_warranty)
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">وضعیت گارانتی:</span>
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-xl text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 animate-pulse">
                                <i class="ti ti-shield-check"></i>
                                تعمیر تحت گارانتی
                            </span>
                        </div>
                        @if($serviceOrder->warranty_id)
                        <div class="flex justify-between items-center group">
                            <span class="text-xs text-slate-400 group-hover:text-slate-600 transition-colors">کد گارانتی:</span>
                            <span class="text-sm font-bold text-amber-700">{{ $serviceOrder->warranty_id }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Fault & Notes -->
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div class="info-group-modern">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                                <i class="ti ti-alert-circle text-xl"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">ایراد اعلام شده</h3>
                        </div>
                        <div class="bg-rose-50/30 p-5 rounded-2xl border border-rose-100 text-sm text-slate-700 leading-relaxed italic pr-8 relative min-h-[100px]">
                            <i class="ti ti-quote text-4xl text-rose-100 absolute right-2 top-2 -z-10"></i>
                            {{ $serviceOrder->fault }}
                        </div>
                    </div>

                    <div class="info-group-modern">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                                <i class="ti ti-note text-xl"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">لوازم همراه و یادداشت</h3>
                        </div>
                        <div class="bg-blue-50/30 p-5 rounded-2xl border border-blue-100 text-sm text-slate-700 leading-relaxed pr-8 relative min-h-[100px]">
                            <i class="ti ti-info-square-rounded text-4xl text-blue-100 absolute right-2 top-2 -z-10"></i>
                            <div class="font-bold text-blue-800 mb-2">لوازم همراه:</div>
                            <p class="mb-3">{{ $serviceOrder->accessories ?? 'موردی ثبت نشده است' }}</p>
                            @if($serviceOrder->notes)
                                <div class="pt-3 border-t border-blue-100">
                                    <div class="font-bold text-blue-800 mb-1 text-xs">توضیحات تکمیلی:</div>
                                    <p>{{ $serviceOrder->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-enhanced-card>

        <!-- Debt Recording Section -->
        @if(auth()->user()->canManageAccounting() || auth()->user()->canAccessAdminPanel())
        <x-enhanced-card title="ثبت بدهی" icon="ti-coin">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-3xl p-6 mb-6">
                <form action="{{ route('automation.service-orders.update', $serviceOrder) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="debt_only" value="1">

                    <div class="debt-record-fields">
                        <div class="debt-record-amount">
                            <label class="block text-xs font-bold text-slate-700 mb-2" for="debt-input">
                                <i class="ti ti-alert-circle text-amber-600 ml-1"></i>
                                میزان بدهی (تومان)
                            </label>
                            <input
                                type="text"
                                id="debt-input"
                                name="debt_amount"
                                class="form-control w-full"
                                value=""
                                inputmode="numeric"
                                autocomplete="off"
                                data-money-input
                                data-money-words="#money-words-debt-amount"
                                data-money-words-url="{{ route('automation.money.words') }}"
                                placeholder="مبلغ بدهی جدید"
                                required
                            >
                            <p id="money-words-debt-amount" class="text-xs font-bold text-slate-500 mt-1 min-h-[1.25rem]"></p>
                        </div>

                        <div class="debt-record-reason">
                            <label class="block text-xs font-bold text-slate-700 mb-2" for="debt-reason-input">
                                <i class="ti ti-note text-amber-600 ml-1"></i>
                                دلیل بدهی
                            </label>
                            <input
                                type="text"
                                id="debt-reason-input"
                                name="debt_reason"
                                class="form-control w-full"
                                value="{{ $serviceOrder->debt_reason ?? '' }}"
                                placeholder="دلیل بدهی را شرح دهید"
                            >
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="btn-modern btn-modern-warning flex items-center gap-2 px-6 py-3">
                            <i class="ti ti-check"></i>
                            <span>ثبت بدهی</span>
                        </button>
                    </div>
                </form>
                @if($serviceOrder->debt_amount && $serviceOrder->debt_amount > 0)
                <div class="mt-4 p-4 bg-white rounded-2xl border border-amber-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-600">وضعیت بدهی:</span>
                        <span class="text-sm font-black text-amber-600">{{ number_format($serviceOrder->debt_amount) }} تومان</span>
                    </div>
                    @if($serviceOrder->debt_reason)
                    <div class="text-xs text-slate-600 italic p-2 bg-amber-50 rounded-lg border border-amber-100">
                        <i class="ti ti-quote ml-1"></i>{{ $serviceOrder->debt_reason }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </x-enhanced-card>
        @endif

        <!-- Repair Items Section -->
        <div id="repair-items-section" data-scroll-section="repair-items-section">
        <x-enhanced-card title="لیست قطعات و خدمات" icon="ti-list-check">
            @php
                $canEditRepairItems = $serviceOrder->canAddRepairItems() && (
                    auth()->user()->isSuperAdmin()
                    || auth()->user()->canManageAccounting()
                    || (
                        auth()->user()->canManageRepairs()
                        && (
                            auth()->user()->isAdmin()
                            || auth()->user()->isReceptionist()
                            || (auth()->user()->isTechnician() && (int) $serviceOrder->technician_id === (int) auth()->id())
                        )
                    )
                );
            @endphp
            @if($serviceOrder->repairItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse" id="repair-items-table">
                    <thead>
                        <tr class="border-b border-slate-100">
                            @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting())
                            <th class="py-4 px-2 text-xs font-bold text-slate-500 w-16 text-center">ردیف</th>
                            @endif
                            <th class="py-4 px-4 text-xs font-bold text-slate-500">شرح کالا / خدمات</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 text-center">تعداد</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 text-center">قیمت واحد</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 text-center">جمع کل</th>
                            @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting())
                                <th class="py-4 px-4 text-xs font-bold text-slate-500 text-center">عملیات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="repair-items-sortable">
                        @foreach($serviceOrder->repairItems as $index => $item)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors repair-item-row" data-item-id="{{ $item->id }}">
                            @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting())
                            <td class="py-4 px-2 text-center">
                                <span class="repair-item-row-num text-xs font-bold text-slate-500">{{ $index + 1 }}</span>
                                <button type="button" class="repair-drag-handle text-slate-400 hover:text-primary-600 cursor-grab active:cursor-grabbing mr-1" title="جابجایی">
                                    <i class="ti ti-grip-vertical"></i>
                                </button>
                            </td>
                            @endif
                            <td class="py-4 px-4">
                                <div class="text-sm font-bold text-slate-700">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="text-[10px] text-slate-400 mt-1">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center text-sm font-medium text-slate-600">{{ $item->quantity }}</td>
                            <td class="py-4 px-4 text-center text-sm font-medium text-slate-600">{{ number_format($item->cost) }}</td>
                            <td class="py-4 px-4 text-center text-sm font-bold text-primary-600">{{ number_format($item->cost * $item->quantity) }}</td>
                            @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting())
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($canEditRepairItems)
                                        <button type="button" onclick="toggleRepairItemEdit({{ $item->id }})" class="text-primary-600 hover:text-primary-800 transition-colors" title="ویرایش">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <form action="{{ route('automation.repairs.remove-item', ['serviceOrder' => $serviceOrder->id, 'repairItem' => $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این آیتم اطمینان دارید؟')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="scroll_to" value="repair-items-section">
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 transition-colors" title="حذف">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-slate-300">—</span>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @if($canEditRepairItems)
                        <tr id="repair-item-edit-{{ $item->id }}" class="hidden bg-slate-50/80 border-b border-slate-100">
                            <td colspan="{{ (auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting()) ? 6 : 5 }}" class="py-4 px-4">
                                <form action="{{ route('automation.repairs.update-item', ['serviceOrder' => $serviceOrder->id, 'repairItem' => $item->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="scroll_to" value="repair-items-section">
                                    @php
                                        $editItemType = $item->item_type === 'labor' ? 'service' : $item->item_type;
                                    @endphp
                                    <div class="md:col-span-2">
                                        <label class="text-[10px] font-bold text-slate-500">نوع</label>
                                        <select name="item_type" class="form-control text-xs w-full" required>
                                            <option value="part" @selected($editItemType === 'part')>قطعه</option>
                                            <option value="service" @selected($editItemType === 'service')>خدمات</option>
                                            <option value="other" @selected($editItemType === 'other')>سایر</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-[10px] font-bold text-slate-500">نام</label>
                                        <input type="text" name="name" value="{{ $item->name }}" class="form-control text-xs w-full" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-[10px] font-bold text-slate-500">تعداد</label>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control text-xs w-full" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-[10px] font-bold text-slate-500">قیمت واحد</label>
                                        <input type="number" name="cost" value="{{ $item->cost }}" min="0" step="1000" class="form-control text-xs w-full">
                                    </div>
                                    <div class="md:col-span-3 flex gap-2">
                                        <button type="submit" class="btn-modern btn-modern-primary btn-sm flex-1">ذخیره</button>
                                        <button type="button" onclick="toggleRepairItemEdit({{ $item->id }})" class="btn-modern btn-modern-secondary btn-sm">انصراف</button>
                                    </div>
                                    @if($item->description)
                                    <div class="md:col-span-12">
                                        <label class="text-[10px] font-bold text-slate-500">توضیحات</label>
                                        <input type="text" name="description" value="{{ $item->description }}" class="form-control text-xs w-full">
                                    </div>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary-50/30">
                            <td colspan="{{ (auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting()) ? 4 : 3 }}" class="py-4 px-4 text-left text-sm font-bold text-slate-700">مجموع کل:</td>
                            <td class="py-4 px-4 text-center text-lg font-black text-primary-600">{{ number_format($serviceOrder->service_cost) }} <span class="text-xs font-normal">تومان</span></td>
                            @if(auth()->user()->canManageRepairs() || auth()->user()->canManageAccounting())
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <i class="ti ti-clipboard-off text-4xl mb-2 opacity-50"></i>
                    <p class="text-sm">هنوز هیچ قطعه یا خدماتی ثبت نشده است.</p>
                </div>
            @endif

            <!-- Add Item Form -->
            @php
                $canEditItems = $serviceOrder->canAddRepairItems() && (
                    auth()->user()->isSuperAdmin()
                    || auth()->user()->canManageAccounting()
                    || auth()->user()->isAdmin()
                    || auth()->user()->isReceptionist()
                    || (auth()->user()->canManageRepairs() && auth()->user()->isTechnician() && (int) $serviceOrder->technician_id === (int) auth()->id())
                    || (auth()->user()->canManageRepairs() && (auth()->user()->isAdmin() || auth()->user()->isReceptionist()))
                );
            @endphp
            @if($canEditItems)
                <div class="mt-8 pt-6 border-t border-slate-100 repair-item-add-form">
                    <h4 class="text-base font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                            <i class="ti ti-plus text-lg"></i>
                        </span>
                        افزودن قطعه یا خدمات
                    </h4>
                    <form action="{{ route('automation.repairs.add-item', $serviceOrder) }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="scroll_to" value="repair-items-section">

                        <div class="space-y-5">
                            <div>
                                <label class="form-label">نوع آیتم</label>
                                <div class="flex rounded-2xl bg-slate-100/80 p-1.5 border border-slate-200 repair-item-type-toggle">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="item_type" value="part" class="peer sr-only" checked onchange="toggleItemType('part')">
                                        <span class="flex items-center justify-center font-semibold rounded-xl text-slate-500 peer-checked:bg-white peer-checked:text-primary-600 peer-checked:shadow-sm transition-all">
                                            <i class="ti ti-cpu ml-1.5"></i> قطعه
                                        </span>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="item_type" value="service" class="peer sr-only" onchange="toggleItemType('service')">
                                        <span class="flex items-center justify-center font-semibold rounded-xl text-slate-500 peer-checked:bg-white peer-checked:text-primary-600 peer-checked:shadow-sm transition-all">
                                            <i class="ti ti-tool ml-1.5"></i> خدمات
                                        </span>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="item_type" value="other" class="peer sr-only" onchange="toggleItemType('other')">
                                        <span class="flex items-center justify-center font-semibold rounded-xl text-slate-500 peer-checked:bg-white peer-checked:text-primary-600 peer-checked:shadow-sm transition-all">
                                            <i class="ti ti-dots ml-1.5"></i> سایر
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div id="inventory-select-container">
                                <label class="form-label">انتخاب قطعه</label>
                                <select name="inventory_id" id="repair-inventory-select" class="form-control w-full" onchange="updatePartPrice(this)">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach($inventoryItems as $item)
                                        <option value="{{ $item->id }}" data-price="{{ (int) round((float) $item->price) }}" data-name="{{ $item->name }}">
                                            {{ $item->name }} (موجودی: {{ $item->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="hidden" id="service-name-container">
                                <label class="form-label">عنوان خدمات</label>
                                <input type="text" id="repair-service-name" class="form-control w-full" placeholder="مثلاً: تعویض ال‌سی‌دی">
                            </div>

                            <div class="hidden" id="other-name-container">
                                <label class="form-label">عنوان (سایر)</label>
                                <input type="text" id="repair-other-name" class="form-control w-full" placeholder="مثلاً: هزینه ایاب و ذهاب">
                            </div>

                            <div class="repair-item-fields-row items-start">
                                <div>
                                    <label class="form-label">تعداد</label>
                                    <input type="number" name="quantity" class="form-control w-full text-center" value="1" min="1" required>
                                </div>

                                <div>
                                    <label class="form-label" for="repair-cost-input">هزینه واحد (تومان)</label>
                                    <input
                                        type="text"
                                        name="cost"
                                        id="repair-cost-input"
                                        class="form-control w-full"
                                        value="0"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        data-money-input
                                        data-money-words="#money-words-repair-cost"
                                        data-money-words-url="{{ route('automation.money.words') }}"
                                        {{ auth()->user()->canManageAccounting() || auth()->user()->canAccessAdminPanel() ? 'required' : '' }}
                                    >
                                    <p id="money-words-repair-cost" class="text-xs font-bold text-slate-500 mt-1 min-h-[1.25rem]"></p>
                                    @unless(auth()->user()->canManageAccounting() || auth()->user()->canAccessAdminPanel())
                                    <p class="text-xs text-slate-400 mt-1.5">در صورت عدم ورود، مبلغ صفر ثبت می‌شود.</p>
                                    @endunless
                                </div>

                                <div>
                                    <label class="form-label">توضیحات (اختیاری)</label>
                                    <input type="text" name="description" class="form-control w-full" placeholder="توضیحات تکمیلی...">
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="btn-modern btn-modern-primary w-full justify-center py-4 text-lg font-bold">
                                    <i class="ti ti-plus"></i>
                                    افزودن
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </x-enhanced-card>
        </div>

        <div id="attachments-section" data-scroll-section="attachments-section">
        <x-enhanced-card title="مستندات و فایل‌های ضمیمه" icon="ti-paperclip">
            @if(auth()->user()->canEditServiceOrders() || auth()->user()->isSuperAdmin())
            <form action="{{ route('automation.service-orders.attachments.store', $serviceOrder) }}" method="POST" enctype="multipart/form-data" class="mb-6 attachment-upload-zone">
                @csrf
                <input type="hidden" name="scroll_to" value="attachments-section">
                <label class="block text-sm font-bold text-slate-700 mb-3">افزودن مستند (در هر مرحله از سفارش)</label>
                <div class="flex flex-wrap items-end gap-4">
                    <input type="file" name="attachments[]" multiple class="form-control flex-1 min-w-[240px]" accept="image/*,.pdf,.doc,.docx">
                    <button type="submit" class="btn-modern btn-modern-primary px-6 py-3">
                        <i class="ti ti-upload"></i>
                        بارگذاری
                    </button>
                </div>
            </form>
            @endif
            @if($serviceOrder->attachments->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($serviceOrder->attachments as $attachment)
                @php
                    $isImage = $attachment->isImage();
                    $isPdf = str_contains((string) $attachment->mime_type, 'pdf');
                    $previewUrl = $attachment->previewUrl();
                @endphp
                <div class="attachment-card group">
                    @if($isImage && $previewUrl)
                        <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="attachment-card-preview block">
                            <img src="{{ $previewUrl }}" alt="{{ $attachment->name }}" loading="lazy">
                            <div class="attachment-card-preview-overlay">
                                <i class="ti ti-zoom-in"></i>
                            </div>
                        </a>
                    @else
                        <div class="attachment-card-file-icon {{ $isPdf ? 'text-rose-500 bg-rose-50/50' : 'text-blue-500 bg-blue-50/50' }}">
                            <i class="ti {{ $isPdf ? 'ti-file-type-pdf' : 'ti-file' }} text-6xl opacity-80"></i>
                        </div>
                    @endif

                    <div class="p-5 flex flex-col flex-1">
                        <div class="text-sm font-bold text-slate-800 truncate mb-2 group-hover:text-primary-600 transition-colors" title="{{ $attachment->name }}">
                            {{ $attachment->name }}
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-database text-slate-400"></i>
                                <span>{{ $attachment->human_readable_size }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>{{ jalali_date($attachment->created_at, 'Y/m/d') }}</span>
                            </div>
                            @if($attachment->uploader)
                            <div class="flex items-center gap-2">
                                <i class="ti ti-user text-slate-400"></i>
                                <span>بارگذاری: {{ $attachment->uploader->name }}</span>
                            </div>
                            @endif
                            @if($attachment->extension)
                            <div class="flex items-center gap-2">
                                <i class="ti ti-file-info text-slate-400"></i>
                                <span class="uppercase">{{ $attachment->extension }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-slate-100">
                            @if($isImage && $previewUrl)
                            <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn-modern btn-modern-secondary btn-sm flex-1 justify-center">
                                <i class="ti ti-eye"></i>
                                پیش‌نمایش
                            </a>
                            @endif
                            <a href="{{ route('automation.attachments.download', $attachment) }}" target="_blank" class="btn-modern btn-modern-primary btn-sm flex-1 justify-center">
                                <i class="ti ti-download"></i>
                                دانلود
                            </a>
                            @if(\Illuminate\Support\Facades\Auth::user()->canEditServiceOrders())
                            <form method="POST" action="{{ route('automation.attachments.destroy', $attachment) }}" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="scroll_to" value="attachments-section">
                                <button type="submit" class="btn-modern btn-modern-danger btn-sm w-full justify-center" onclick="return confirm('آیا از حذف این فایل مطمئن هستید؟')">
                                    <i class="ti ti-trash"></i>
                                    حذف
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-500 text-center py-8">هنوز فایلی پیوست نشده است.</p>
            @endif
        </x-enhanced-card>
        </div>

        @if($serviceOrder->orderLogs->count() > 0)
        <x-enhanced-card title="تاریخچه فعالیت‌ها و رهگیری" icon="ti-history">
            <div class="relative pr-8 before:content-[''] before:absolute before:right-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                @foreach($serviceOrder->orderLogs as $log)
                <div class="relative mb-10 last:mb-0 group">
                    <!-- Timeline Dot -->
                    <div class="absolute -right-[25px] top-1.5 w-4 h-4 rounded-full border-4 border-white bg-slate-200 group-hover:bg-primary-500 group-hover:scale-125 transition-all duration-300 z-10 shadow-sm"></div>
                    
                    <div class="bg-slate-50/50 group-hover:bg-white group-hover:shadow-lg group-hover:shadow-slate-200/50 group-hover:border-slate-200 border border-transparent p-5 rounded-3xl transition-all duration-300">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-primary-600 transition-colors">
                                    <i class="ti ti-activity text-lg"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-slate-700 block">{{ $log->action_name }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-5 h-5 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                                            <i class="ti ti-user text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] text-slate-500">توسط: <span class="font-bold text-slate-600">{{ $log->user->name }}</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-50">
                                <i class="ti ti-calendar-event"></i>
                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at)->format('Y/m/d H:i') }}
                                @else
                                    {{ $log->created_at->format('Y/m/d H:i') }}
                                @endif
                            </div>
                        </div>
                        
                        @if($log->description)
                        <div class="text-xs text-slate-600 leading-relaxed bg-white/80 p-4 rounded-2xl border border-slate-100 italic pr-6 relative">
                            <i class="ti ti-info-circle text-slate-200 absolute right-2 top-4"></i>
                            {{ $log->description }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </x-enhanced-card>
        @endif
    </div>

    <!-- Sidebar Content -->
    <div class="space-y-8">
        <!-- Status & Main Action Card -->
        <x-enhanced-card title="وضعیت جاری" icon="ti-settings">
            <div class="flex flex-col items-center py-8">
                <div class="mb-8 relative">
                    <div class="absolute inset-0 bg-primary-500/10 blur-3xl rounded-full animate-pulse"></div>
                    <x-enhanced-status-badge :status="$serviceOrder->status ?? 'registered'" size="lg" />
                </div>

                @if($serviceOrder->technician_id && in_array($serviceOrder->status, [\App\Enums\ServiceOrderStatus::TECHNICIAN_ASSIGNED, \App\Enums\ServiceOrderStatus::REGISTERED], true))
                <div class="w-full px-2">
                    <p class="text-xs text-slate-500 text-center mb-3">پس از تخصیص تکنسین، برای شروع کار روی دکمه «شروع تعمیر» در بالای صفحه کلیک کنید.</p>
                </div>
                @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::REGISTERED)
                <div class="w-full px-2">
                    <p class="text-xs text-amber-600 text-center bg-amber-50 rounded-xl p-3 border border-amber-100">ابتدا باید تکنسین به این سفارش تخصیص یابد.</p>
                </div>
                @endif

                @if(in_array($serviceOrder->status, [\App\Enums\ServiceOrderStatus::READY, \App\Enums\ServiceOrderStatus::DELIVERED]))
                <div class="w-full p-5 bg-amber-50/50 border border-amber-100 rounded-3xl text-amber-700 text-[11px] leading-relaxed flex gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-lock text-amber-600 text-xl"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <div class="font-bold mb-1">سفارش نهایی شده</div>
                        <p>اطلاعات این سفارش به دلیل رسیدن به وضعیت نهایی قابل ویرایش نیست.</p>
                    </div>
                </div>
                @endif
            </div>
        </x-enhanced-card>

        <!-- Time Timeline -->
        <x-enhanced-card title="زمان‌بندی و مهلت" icon="ti-clock">
            <div class="space-y-6">
                <div class="flex gap-4 group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary-50 group-hover:text-primary-500 transition-all duration-300">
                        <i class="ti ti-plus text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1 font-bold">تاریخ ثبت اولیه</div>
                        <div class="text-sm font-black text-slate-700">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at->timezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                            @else
                                {{ $serviceOrder->created_at->timezone('Asia/Tehran')->format('Y/m/d H:i') }}
                            @endif
                        </div>
                    </div>
                </div>

                @if($serviceOrder->repair_started_at)
                <div class="flex gap-4 group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-all duration-300">
                        <i class="ti ti-player-play text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1 font-bold">زمان شروع تعمیر</div>
                        <div class="text-sm font-black text-slate-700">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->repair_started_at->timezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                            @else
                                {{ $serviceOrder->repair_started_at->timezone('Asia/Tehran')->format('Y/m/d H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($serviceOrder->repair_completed_at)
                <div class="flex gap-4 group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-all duration-300">
                        <i class="ti ti-check text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1 font-bold">اتمام و آماده‌سازی</div>
                        <div class="text-sm font-black text-slate-700">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->repair_completed_at->timezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                            @else
                                {{ $serviceOrder->repair_completed_at->timezone('Asia/Tehran')->format('Y/m/d H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </x-enhanced-card>

        <!-- SMS History -->
        <x-enhanced-card title="تاریخچه پیامک‌ها" icon="ti-message-dots" animated>
            <div class="space-y-4">
                @forelse($smsLogs as $log)
                <div class="group p-4 bg-white border border-slate-100 rounded-3xl hover:border-primary-200 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        @php
                            $statusType = $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : ($log->status == 'error' ? 'danger' : 'warning'));
                            $statusLabel = $log->status == 'sent' ? 'ارسال شد' : ($log->status == 'failed' ? 'خطای پنل' : ($log->status == 'error' ? 'خطای سیستم' : 'در انتظار'));
                        @endphp
                        <x-enhanced-status-badge :status="$statusType" :label="$statusLabel" size="xs" />
                        <span class="text-[10px] text-slate-400 font-bold">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at->timezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                            @else
                                {{ $log->created_at->timezone('Asia/Tehran')->format('Y/m/d H:i') }}
                            @endif
                        </span>
                    </div>
                    <div class="text-xs text-slate-600 leading-relaxed mb-2">
                        {{ $log->message }}
                    </div>
                    @if($log->error_message)
                    <div class="p-2 bg-rose-50 rounded-xl border border-rose-100">
                        <div class="flex items-center gap-1.5 text-rose-600 mb-1">
                            <i class="ti ti-alert-circle text-sm"></i>
                            <span class="text-[10px] font-black">علت خطا: {{ $log->error_code }}</span>
                        </div>
                        <p class="text-[10px] text-rose-500 font-bold leading-relaxed">{{ $log->error_message }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-6 text-slate-400 text-xs font-bold">
                    @if(empty($serviceOrder->receiver_phone))
                        شماره گیرنده پیامک ثبت نشده — پیامکی ارسال نشده است.
                    @else
                        هیچ پیامکی برای این سفارش ثبت نشده است.
                    @endif
                </div>
                @endforelse
            </div>
        </x-enhanced-card>

        <!-- Management Actions -->
        <x-enhanced-card title="مدیریت سفارش" icon="ti-settings-2">
            <div class="space-y-4">
                @if(\Illuminate\Support\Facades\Auth::user()->canEditServiceOrders() && $serviceOrder->canBeEdited())
                    <a href="{{ route('automation.service-orders.edit', $serviceOrder) }}" class="btn-modern btn-modern-secondary w-full justify-center group py-3">
                        <i class="ti ti-edit group-hover:scale-110 transition-transform text-lg"></i>
                        ویرایش اطلاعات
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Auth::user()->canAccessAdminPanel())
                    <form method="POST" action="{{ route('automation.service-orders.destroy', $serviceOrder) }}" class="block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-modern btn-modern-danger w-full justify-center group py-3" onclick="return confirm('آیا از حذف این سفارش مطمئن هستید؟ این عمل غیرقابل بازگشت است.')">
                            <i class="ti ti-trash group-hover:shake transition-all text-lg"></i>
                            حذف کامل سفارش
                        </button>
                    </form>
                @endif
            </div>
        </x-enhanced-card>

        <!-- Quick Info Badge -->
        <div class="p-6 bg-gradient-to-br from-primary-600 to-primary-700 rounded-[2rem] text-white shadow-xl shadow-primary-200 relative overflow-hidden group">
            <i class="ti ti-shield-check text-9xl absolute -right-8 -bottom-8 opacity-10 group-hover:scale-110 transition-transform duration-700"></i>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mb-4">
                    <i class="ti ti-shield-check text-2xl"></i>
                </div>
                <h4 class="text-lg font-black mb-2">اطمینان از کیفیت</h4>
                <p class="text-xs text-primary-100 leading-relaxed opacity-80">تمامی خدمات ارائه شده توسط پارس لیان دارای ضمانت کیفیت و پشتیبانی فنی می‌باشد.</p>
            </div>
        </div>
    </div>
</div>
</div> <!-- End of screen-only -->

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}
.group-hover\:shake:hover {
    animation: shake 0.2s ease-in-out infinite;
}
</style>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="reject-modal-backdrop"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="reject-modal-panel">
                <form action="{{ route('automation.repairs.reject', $serviceOrder) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="ti ti-x text-rose-600 text-xl"></i>
                            </div>
                            <div class="mt-3 text-center sm:mr-4 sm:mt-0 sm:text-right w-full">
                                <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">رد سفارش تعمیر</h3>
                                <div class="mt-4">
                                    <label for="reject-reason" class="block text-sm font-medium text-slate-700 mb-2">دلیل رد سفارش</label>
                                    <textarea name="reason" id="reject-reason" rows="3" class="form-control w-full text-sm" placeholder="علت رد سفارش را بنویسید..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="submit" class="btn-modern btn-modern-danger w-full sm:w-auto">
                            ثبت و رد سفارش
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="btn-modern btn-modern-secondary w-full sm:w-auto">
                            انصراف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
