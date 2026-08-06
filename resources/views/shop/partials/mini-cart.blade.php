<div class="mini-cart-panel">

    {{-- Header --}}
    <div class="mini-cart-header">
        <div class="mini-cart-title">
            <i class="ti ti-shopping-cart text-blue-500 text-lg"></i>
            <span>سبد خرید</span>
        </div>
        <span class="mini-cart-badge">{{ ($cart->items ?? collect())->count() }} محصول</span>
    </div>

    @if(($cart->items ?? collect())->count() > 0)

        {{-- Items list --}}
        <div class="mini-cart-items">
            @foreach($cart->items as $item)
                <div class="mini-cart-item group">
                    <div class="mini-cart-item-img">
                        <img loading="lazy"
                             src="{{ $item->product->main_image_url }}"
                             alt="{{ $item->product->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="mini-cart-item-info">
                        <h4 class="mini-cart-item-name">
                            <a href="{{ route('catalog.show', $item->product->slug) }}"
                               class="hover:text-blue-600 transition-colors">{{ $item->product->name }}</a>
                        </h4>
                        <div class="mini-cart-item-meta">
                            <span class="mini-cart-item-qty">{{ $item->quantity }} عدد</span>
                            <span class="mini-cart-item-price">{{ number_format($item->price) }} تومان</span>
                        </div>
                    </div>
                    <button type="button"
                            onclick="removeFromMiniCart(this, '{{ $item->product->slug }}')"
                            class="mini-cart-remove"
                            aria-label="حذف از سبد">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mini-cart-footer">
            <div class="mini-cart-total">
                <span class="mini-cart-total-label">مجموع:</span>
                <span class="mini-cart-total-value">{{ number_format($cart->total) }} تومان</span>
            </div>
            <div class="mini-cart-actions">
                <a href="{{ route('cart.index') }}" class="mini-cart-btn-secondary">مشاهده سبد</a>
                <a href="{{ route('checkout.index') }}" class="mini-cart-btn-primary">تسویه حساب</a>
            </div>
        </div>

    @else

        {{-- Empty state --}}
        <div class="mini-cart-empty">
            <div class="mini-cart-empty-icon">
                <i class="ti ti-shopping-cart-off text-3xl"></i>
            </div>
            <p class="mini-cart-empty-text">سبد خرید شما خالی است</p>
            <a href="{{ route('shop.index') }}" class="mini-cart-empty-link">مشاهده فروشگاه</a>
        </div>

    @endif

</div>

<style>
.mini-cart-panel {
    padding: 1rem;
    font-family: var(--font-sans, 'Vazirmatn', sans-serif);
}

.mini-cart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.875rem;
    margin-bottom: 0.875rem;
    border-bottom: 1px solid #f3f4f6;
}

.mini-cart-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 800;
    color: #111827;
}

.mini-cart-badge {
    background: #eff6ff;
    color: #2563eb;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 0.5rem;
}

.mini-cart-items {
    max-height: 16rem;
    overflow-y: auto;
    margin-bottom: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.mini-cart-items::-webkit-scrollbar { width: 3px; }
.mini-cart-items::-webkit-scrollbar-track { background: transparent; }
.mini-cart-items::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 2px; }

.mini-cart-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mini-cart-item-img {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 0.75rem;
    overflow: hidden;
    flex-shrink: 0;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
}

.mini-cart-item-info {
    flex: 1;
    min-width: 0;
}

.mini-cart-item-name {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 0.25rem;
}

.mini-cart-item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mini-cart-item-qty {
    font-size: 0.75rem;
    color: #9ca3af;
}

.mini-cart-item-price {
    font-size: 0.8125rem;
    font-weight: 800;
    color: #2563eb;
}

.mini-cart-remove {
    padding: 0.25rem;
    color: #d1d5db;
    background: none;
    border: none;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: color 0.2s, background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mini-cart-remove:hover {
    color: #ef4444;
    background: #fef2f2;
}

.mini-cart-footer {
    border-top: 1px solid #f3f4f6;
    padding-top: 0.875rem;
}

.mini-cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.mini-cart-total-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.mini-cart-total-value {
    font-size: 1rem;
    font-weight: 800;
    color: #111827;
}

.mini-cart-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.mini-cart-btn-secondary {
    padding: 0.625rem;
    background: #f3f4f6;
    color: #374151;
    text-align: center;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.8125rem;
    text-decoration: none;
    transition: background 0.2s;
}

.mini-cart-btn-secondary:hover { background: #e5e7eb; }

.mini-cart-btn-primary {
    padding: 0.625rem;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    color: #fff;
    text-align: center;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.8125rem;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.2s;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
}

.mini-cart-btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.mini-cart-empty {
    padding: 2.5rem 1rem;
    text-align: center;
}

.mini-cart-empty-icon {
    width: 4rem;
    height: 4rem;
    background: #f9fafb;
    color: #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.mini-cart-empty-text {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-bottom: 0.75rem;
}

.mini-cart-empty-link {
    display: inline-block;
    font-size: 0.875rem;
    font-weight: 700;
    color: #2563eb;
    text-decoration: none;
    padding: 0.5rem 1.25rem;
    background: #eff6ff;
    border-radius: 0.75rem;
    transition: background 0.2s;
}

.mini-cart-empty-link:hover { background: #dbeafe; }
</style>
