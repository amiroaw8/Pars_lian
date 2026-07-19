@php
    $isEdit = isset($product) && $product;
    $selectedInventoryId = old('inventory_id', $isEdit ? $product->inventory_id : null);
    $isLinked = (bool) $selectedInventoryId;
    $linkedInv = $linkedInventory ?? ($isEdit && $product->inventory_id ? \App\Models\Inventory::find($product->inventory_id) : null);
@endphp

<div class="md:col-span-2 bg-blue-50/50 p-6 rounded-2xl border border-blue-100 space-y-4" id="inventory-link-card">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="block text-xs font-bold text-blue-500 uppercase tracking-wider flex items-center gap-2">
            <i class="ti ti-package"></i>
            اتصال به انبار (اختیاری)
        </label>
        <span id="inventory-link-status"
            class="text-[10px] font-black px-3 py-1.5 rounded-full border {{ $isLinked ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200' }}">
            {{ $isLinked ? 'هماهنگ با انبار' : 'مستقل از انبار' }}
        </span>
    </div>

    @if($isEdit && $linkedInv)
        <div id="inventory-link-summary" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold">
            متصل به: {{ $linkedInv->name }} —
            موجودی انبار: <span id="inventory-qty-display">{{ $linkedInv->quantity }}</span> /
            فروشگاه: <span id="shop-qty-display">{{ $product->stock_quantity }}</span>
        </div>
    @elseif($isEdit && ($suggestedInventory ?? null))
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
            <span class="font-black">پیشنهاد:</span> کالای انبار با SKU یکسان —
            <button type="button" class="underline font-bold" onclick="ProductInventoryLink.selectInventory({{ $suggestedInventory->id }})">
                {{ $suggestedInventory->name }} ({{ $suggestedInventory->quantity }} عدد)
            </button>
        </div>
    @else
        <div id="inventory-link-summary" class="p-3 rounded-xl {{ $isLinked ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600' }} border text-xs font-bold {{ $isLinked ? '' : 'hidden' }}">
            <span id="inventory-link-summary-text"></span>
        </div>
    @endif

    <div class="relative">
        <select name="inventory_id" id="inventory_select"
            class="w-full bg-white border border-blue-200 rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer"
            onchange="ProductInventoryLink.onSelectChange()">
            <option value="">-- بدون اتصال به انبار --</option>
            @foreach($inventories as $inv)
                <option value="{{ $inv->id }}"
                    data-name="{{ $inv->name }}"
                    data-sku="{{ $inv->sku }}"
                    data-quantity="{{ $inv->quantity }}"
                    {{ ($inv->taken_by_other ?? false) ? 'disabled' : '' }}
                    {{ (string) $selectedInventoryId === (string) $inv->id ? 'selected' : '' }}>
                    {{ $inv->name }}
                    @if($inv->device_code) (کد: {{ $inv->device_code }}) @endif
                    (موجودی: {{ $inv->quantity }}) - SKU: {{ $inv->sku ?? 'ندارد' }}
                    @if($inv->taken_by_other ?? false) — متصل به محصول دیگر @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="button" id="btn-sync-inventory"
            onclick="ProductInventoryLink.syncNow()"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-black bg-blue-600 text-white hover:bg-blue-700 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
            {{ $isLinked ? '' : 'disabled' }}>
            <i class="ti ti-refresh"></i>
            هماهنگ‌سازی با انبار
        </button>
        <button type="button" id="btn-detach-inventory"
            onclick="ProductInventoryLink.detach()"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-black bg-white text-rose-600 border border-rose-200 hover:bg-rose-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
            {{ $isLinked ? '' : 'disabled' }}>
            <i class="ti ti-unlink"></i>
            جداسازی از انبار
        </button>
    </div>

    <p id="inventory-link-hint" class="text-xs text-blue-500 flex items-start gap-1">
        <i class="ti ti-info-circle mt-0.5 shrink-0"></i>
        <span>
            @if($isLinked)
                در حالت هماهنگ، موجودی فروشگاه از انبار خوانده می‌شود و فروش/تعمیر هر دو را به‌روز می‌کند.
            @else
                در حالت مستقل، موجودی فروشگاه جدا از انبار است و فقط از همین فرم قابل تغییر است.
            @endif
        </span>
    </p>

    <input type="hidden" name="inventory_unlinked" id="inventory_unlinked" value="{{ $isLinked ? '0' : '1' }}">

    @if($isEdit)
        <div class="border-t border-blue-100 pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-black text-blue-600 uppercase tracking-wider">تاریخچه رویدادها</h4>
                <a href="{{ route('admin.products.history', $product) }}" class="text-xs font-bold text-blue-500 hover:text-blue-700">مشاهده همه</a>
            </div>
            @include('admin.products.partials.activity-timeline', ['activities' => $recentActivities ?? collect()])
        </div>
    @endif
</div>

@once
@push('scripts')
<script>
window.ProductInventoryLink = (function () {
    const cfg = {
        isEdit: @json($isEdit),
        initialInventoryId: @json((string) ($selectedInventoryId ?? '')),
        syncUrl: @json($isEdit ? route('admin.products.sync-inventory', $product) : null),
        detachUrl: @json($isEdit ? route('admin.products.detach-inventory', $product) : null),
        csrf: @json(csrf_token()),
    };

    function els() {
        return {
            select: document.getElementById('inventory_select'),
            stock: document.getElementById('stock_quantity_input'),
            status: document.getElementById('inventory-link-status'),
            hint: document.getElementById('inventory-link-hint'),
            summary: document.getElementById('inventory-link-summary'),
            summaryText: document.getElementById('inventory-link-summary-text'),
            unlinked: document.getElementById('inventory_unlinked'),
            btnSync: document.getElementById('btn-sync-inventory'),
            btnDetach: document.getElementById('btn-detach-inventory'),
            invQty: document.getElementById('inventory-qty-display'),
            shopQty: document.getElementById('shop-qty-display'),
        };
    }

    function selectedOption() {
        const { select } = els();
        if (!select) return null;
        return select.options[select.selectedIndex];
    }

    function setLinkedUi(linked) {
        const e = els();
        if (e.status) {
            e.status.textContent = linked ? 'هماهنگ با انبار' : 'مستقل از انبار';
            e.status.className = linked
                ? 'text-[10px] font-black px-3 py-1.5 rounded-full border bg-emerald-100 text-emerald-700 border-emerald-200'
                : 'text-[10px] font-black px-3 py-1.5 rounded-full border bg-amber-100 text-amber-800 border-amber-200';
        }
        if (e.unlinked) e.unlinked.value = linked ? '0' : '1';
        if (e.btnSync) e.btnSync.disabled = !linked;
        if (e.btnDetach) e.btnDetach.disabled = !linked;
        if (e.hint) {
            e.hint.querySelector('span').textContent = linked
                ? 'در حالت هماهنگ، موجودی فروشگاه از انبار خوانده می‌شود و فروش/تعمیر هر دو را به‌روز می‌کند.'
                : 'در حالت مستقل، موجودی فروشگاه جدا از انبار است و فقط از همین فرم قابل تغییر است.';
        }
        if (e.stock) {
            e.stock.readOnly = linked;
            e.stock.classList.toggle('opacity-70', linked);
            e.stock.classList.toggle('cursor-not-allowed', linked);
        }
    }

    function updateSummary() {
        const opt = selectedOption();
        const e = els();
        if (!opt || !opt.value) {
            if (e.summary && e.summaryText) {
                e.summary.classList.add('hidden');
            }
            return;
        }
        const name = opt.getAttribute('data-name');
        const qty = opt.getAttribute('data-quantity');
        if (e.summary && e.summaryText) {
            e.summary.classList.remove('hidden');
            e.summary.className = 'p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold border';
            e.summaryText.textContent = `متصل به: ${name} — موجودی انبار: ${qty}`;
        }
    }

    function applyNameSkuFromOption(opt) {
        if (!opt || !opt.value) return;
        const name = opt.getAttribute('data-name');
        const sku = opt.getAttribute('data-sku');
        const nameInput = document.getElementById('product_name') || document.querySelector('input[name="name"]');
        const skuInput = document.getElementById('product_sku') || document.querySelector('input[name="sku"]');
        if (nameInput && name) nameInput.value = name;
        if (skuInput && sku) skuInput.value = sku;
    }

    function applyOptionToForm(opt, fillMeta) {
        if (!opt || !opt.value) return;
        const qty = opt.getAttribute('data-quantity');
        const e = els();
        if (e.stock && qty !== null) e.stock.value = qty;
        if (fillMeta) {
            applyNameSkuFromOption(opt);
        }
    }

    function inventorySelectionChanged() {
        const opt = selectedOption();
        const currentId = opt && opt.value ? String(opt.value) : '';
        return currentId !== String(cfg.initialInventoryId || '');
    }

    function onSelectChange() {
        const opt = selectedOption();
        const linked = !!(opt && opt.value);
        setLinkedUi(linked);
        if (linked) {
            applyOptionToForm(opt, !cfg.isEdit);
            if (cfg.isEdit && inventorySelectionChanged()) {
                if (confirm('نام و SKU محصول با انبار هماهنگ شود؟')) {
                    applyNameSkuFromOption(opt);
                }
            }
            updateSummary();
        } else {
            updateSummary();
        }
    }

    function selectInventory(id) {
        const { select } = els();
        if (!select) return;
        select.value = String(id);
        onSelectChange();
    }

    async function syncNow() {
        const opt = selectedOption();
        if (!opt || !opt.value) {
            alert('ابتدا یک کالا از انبار انتخاب کنید.');
            return;
        }

        if (cfg.isEdit && cfg.syncUrl) {
            try {
                const res = await fetch(cfg.syncUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'هماهنگ‌سازی انجام نشد.');
                    return;
                }
                const e = els();
                if (e.stock) e.stock.value = data.stock_quantity;
                if (e.invQty) e.invQty.textContent = data.inventory_quantity;
                if (e.shopQty) e.shopQty.textContent = data.stock_quantity;
                opt.setAttribute('data-quantity', data.inventory_quantity);
                alert(data.message || 'هماهنگ‌سازی انجام شد.');
            } catch (err) {
                alert('خطا در ارتباط با سرور.');
            }
            return;
        }

        applyOptionToForm(opt, true);
        applyNameSkuFromOption(opt);
        updateSummary();
        alert('موجودی و اطلاعات پایه با انبار هماهنگ شد. پس از ذخیره، اتصال فعال می‌شود.');
    }

    async function detach() {
        if (!confirm('محصول از انبار جدا شود؟ موجودی فعلی فروشگاه حفظ می‌شود و دیگر با انبار هماهنگ نخواهد بود.')) {
            return;
        }

        if (cfg.isEdit && cfg.detachUrl) {
            try {
                const res = await fetch(cfg.detachUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'جداسازی انجام نشد.');
                    return;
                }
                const e = els();
                if (e.select) e.select.value = '';
                if (e.stock) e.stock.value = data.stock_quantity;
                setLinkedUi(false);
                updateSummary();
                alert(data.message || 'از انبار جدا شد.');
            } catch (err) {
                alert('خطا در ارتباط با سرور.');
            }
            return;
        }

        const e = els();
        if (e.select) e.select.value = '';
        setLinkedUi(false);
        updateSummary();
    }

    document.addEventListener('DOMContentLoaded', function () {
        onSelectChange();
    });

    return { onSelectChange, selectInventory, syncNow, detach };
})();
</script>
@endpush
@endonce
