@extends('layouts.admin')

@section('title', 'فروش دستی (حضوری) - پارس لیان')

@section('content')
<div class="pos-container animate-fade-in">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-180px)]">
        
        <!-- محصولات (8 ستون) -->
        <div class="lg:col-span-8 flex flex-col gap-6 h-full">
            <x-enhanced-card title="جستجو و انتخاب کالا" icon="ti ti-search" class="flex-shrink-0">
                <div class="relative">
                    <input type="text" id="product-search" 
                        class="w-full h-14 pr-12 pl-4 rounded-2xl border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all text-lg font-bold"
                        placeholder="نام کالا یا بارکد (SKU) را وارد کنید...">
                    <div class="absolute right-0 top-0 h-14 w-12 flex items-center justify-center text-slate-400">
                        <i class="ti ti-scan text-2xl"></i>
                    </div>
                </div>
            </x-enhanced-card>

            <div class="flex-1 min-h-0 overflow-y-auto pr-2 custom-scrollbar">
                <div id="product-list" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                    @php
                        $productPayload = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => (int) round((float) $product->current_price),
                            'sku' => $product->sku,
                        ];
                    @endphp
                    <div class="product-card group bg-white rounded-3xl p-4 border border-slate-100 shadow-sm hover:shadow-xl hover:border-primary-500 transition-all duration-300 cursor-pointer"
                         data-product="{{ json_encode($productPayload, JSON_UNESCAPED_UNICODE) }}"
                         onclick="addProductFromEl(this)">
                        <div class="relative pt-[100%] rounded-2xl bg-slate-50 mb-3 overflow-hidden border border-slate-50">
                            <img loading="lazy" src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
                                 class="absolute inset-0 w-full h-full object-contain p-3 group-hover:scale-110 transition-transform duration-500"
                                 onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1 truncate">{{ $product->name }}</h3>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[10px] text-slate-400 font-mono">{{ $product->sku }}</span>
                            <span class="text-primary-600 font-black text-sm">{{ number_format((int) round($product->current_price)) }} <span class="text-[10px] font-normal">تومان</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- سبد خرید و مشتری (4 ستون) -->
        <div class="lg:col-span-4 flex flex-col gap-6 h-full">
            <!-- انتخاب مشتری -->
            <x-enhanced-card title="اطلاعات مشتری" icon="ti ti-user" class="flex-shrink-0">
                <select id="customer-select" class="form-control h-12 rounded-xl text-sm font-bold">
                    <option value="">انتخاب مشتری...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                    @endforeach
                </select>
            </x-enhanced-card>

            <!-- لیست سبد خرید -->
            <div class="flex-1 bg-white rounded-[2.5rem] shadow-xl border border-slate-100 flex flex-col overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <i class="ti ti-shopping-cart text-primary-600"></i>
                        سبد فروش
                    </h3>
                    <span id="cart-count" class="bg-primary-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">0 آیتم</span>
                </div>

                <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                    <div id="empty-cart" class="h-full flex flex-col items-center justify-center text-slate-300 gap-4 opacity-50">
                        <i class="ti ti-shopping-cart-off text-6xl"></i>
                        <p class="text-sm font-bold">سبد خرید خالی است</p>
                    </div>
                </div>

                <!-- مبالغ و دکمه پرداخت -->
                <div class="p-8 bg-slate-50 border-t border-slate-100 space-y-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-slate-500">
                            <span class="text-sm font-bold">جمع کل:</span>
                            <span id="total-amount" class="text-xl font-black text-slate-800">0 تومان</span>
                        </div>
                        <p id="total-amount-words" class="text-xs font-bold text-slate-500 min-h-[1rem]"></p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" onclick="checkout('cash')" class="btn-modern btn-modern-success py-4 justify-center shadow-lg shadow-emerald-500/20 group">
                            <i class="ti ti-cash text-xl group-hover:scale-125 transition-transform"></i>
                            نقد
                        </button>
                        <button type="button" onclick="checkout('card')" class="btn-modern btn-modern-primary py-4 justify-center shadow-lg shadow-primary-500/20 group">
                            <i class="ti ti-credit-card text-xl group-hover:scale-125 transition-transform"></i>
                            کارتخوان
                        </button>
                        <button type="button" onclick="checkout('debt')" class="btn-modern btn-modern-warning py-4 justify-center shadow-lg shadow-amber-500/20 group">
                            <i class="ti ti-receipt-tax text-xl group-hover:scale-125 transition-transform"></i>
                            ثبت بدهی
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    
    .product-card:active { transform: scale(0.95); }
    .cart-item-animate { animation: slideIn 0.3s ease-out forwards; }
    #empty-cart.hidden { display: none !important; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

@push('scripts')
<script>
    let cart = [];
    const moneyWordsUrl = @json(route('automation.money.words'));

    function formatToman(amount) {
        return Math.round(amount).toLocaleString('en-US');
    }

    function addProductFromEl(el) {
        const product = JSON.parse(el.getAttribute('data-product'));
        addToCart(product);
    }

    function addToCart(product) {
        const id = Number(product.id);
        const price = Math.round(parseFloat(product.price ?? product.current_price ?? 0) || 0);
        const existing = cart.find(item => Number(item.id) === id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({ id, name: product.name, price, quantity: 1 });
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => Number(item.id) !== Number(id));
        renderCart();
    }

    function updateQuantity(id, delta) {
        const item = cart.find(i => Number(i.id) === Number(id));
        if (!item) return;
        item.quantity += delta;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            renderCart();
        }
    }

    async function updateTotalWords(total) {
        const wordsEl = document.getElementById('total-amount-words');
        if (!wordsEl) return;
        if (total <= 0) {
            wordsEl.textContent = '';
            return;
        }
        try {
            const res = await fetch(`${moneyWordsUrl}?amount=${total}`);
            if (res.ok) {
                const data = await res.json();
                wordsEl.textContent = data.words || '';
            }
        } catch (_) {
            wordsEl.textContent = '';
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart');
        const totalEl = document.getElementById('total-amount');
        const countEl = document.getElementById('cart-count');

        container.querySelectorAll('.cart-line').forEach(el => el.remove());

        if (cart.length === 0) {
            emptyMsg.classList.remove('hidden');
            totalEl.textContent = '0 تومان';
            countEl.textContent = '0 آیتم';
            updateTotalWords(0);
            return;
        }

        emptyMsg.classList.add('hidden');

        let total = 0;
        let totalQty = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            totalQty += item.quantity;

            const div = document.createElement('div');
            div.className = 'cart-line cart-item-animate p-4 rounded-2xl bg-white border border-slate-100 flex justify-between items-center group hover:border-primary-200 transition-all duration-300';
            div.innerHTML = `
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-bold text-slate-700">${escapeHtml(item.name)}</span>
                    <span class="text-xs text-primary-600 font-black">${formatToman(item.price)} تومان</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-slate-50 p-1 rounded-xl border border-slate-100">
                        <button type="button" data-cart-action="plus" data-product-id="${item.id}" class="w-7 h-7 rounded-lg bg-white text-slate-600 flex items-center justify-center hover:text-primary-600 shadow-sm transition-all"><i class="ti ti-plus"></i></button>
                        <span class="w-8 text-center text-sm font-black text-slate-700">${item.quantity}</span>
                        <button type="button" data-cart-action="minus" data-product-id="${item.id}" class="w-7 h-7 rounded-lg bg-white text-slate-600 flex items-center justify-center hover:text-rose-600 shadow-sm transition-all"><i class="ti ti-minus"></i></button>
                    </div>
                    <button type="button" data-cart-action="remove" data-product-id="${item.id}" class="text-slate-300 hover:text-rose-500 transition-colors"><i class="ti ti-trash"></i></button>
                </div>
            `;
            container.appendChild(div);
        });

        totalEl.textContent = formatToman(total) + ' تومان';
        countEl.textContent = totalQty + ' آیتم';
        updateTotalWords(Math.round(total));
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('cart-items').addEventListener('click', function (e) {
        const btn = e.target.closest('[data-cart-action]');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();

        const id = btn.dataset.productId;
        const action = btn.dataset.cartAction;

        if (action === 'plus') updateQuantity(id, 1);
        else if (action === 'minus') updateQuantity(id, -1);
        else if (action === 'remove') removeFromCart(id);
    });

    async function checkout(method) {
        const customerId = document.getElementById('customer-select').value;
        if (!customerId) {
            alert('لطفاً ابتدا مشتری را انتخاب کنید.');
            return;
        }
        if (cart.length === 0) {
            alert('سبد خرید خالی است.');
            return;
        }

        if (!confirm(`آیا از ثبت نهایی فروش به مبلغ ${document.getElementById('total-amount').textContent} اطمینان دارید؟`)) return;

        const paymentMethod = method === 'cash' ? 'cod' : method;

        try {
            const response = await fetch("{{ route('automation.pos.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    items: cart.map(i => ({ id: i.id, quantity: i.quantity, price: i.price })),
                    payment_method: paymentMethod
                })
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message || 'فروش با موفقیت ثبت شد.');
                cart = [];
                renderCart();
                if (result.redirect_url) {
                    window.location.href = result.redirect_url;
                } else {
                    window.location.reload();
                }
            } else {
                alert('خطا: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('خطا در برقراری ارتباط با سرور.');
        }
    }

    document.getElementById('product-search').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const sku = card.querySelector('.font-mono').textContent.toLowerCase();
            card.style.display = (name.includes(q) || sku.includes(q)) ? 'block' : 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', () => renderCart());
</script>
@endpush
@endsection
