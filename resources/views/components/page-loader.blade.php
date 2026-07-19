@props([
    'logoSize' => 'lg',
    'logoClass' => 'h-14 max-w-[180px] rounded-xl',
])

@once
<style>
    .page-loader {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        background: radial-gradient(ellipse at 50% 40%, #1e3a5f 0%, #0f172a 55%, #020617 100%);
        transition: opacity 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                    visibility 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                    transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-loader.hide {
        opacity: 0;
        visibility: hidden;
        transform: scale(1.04);
        pointer-events: none;
    }

    .loader-content {
        text-align: center;
        color: #fff;
        padding: 1rem;
    }

    .loader-visual {
        position: relative;
        width: 9.5rem;
        height: 9.5rem;
        margin: 0 auto 1.25rem;
        display: grid;
        place-items: center;
    }

    .loader-spinner {
        position: absolute;
        inset: 0;
        margin: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .loader-spinner-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid rgba(59, 130, 246, 0.12);
        border-top-color: #3b82f6;
        border-right-color: rgba(96, 165, 250, 0.55);
        animation: loaderRingSpin 1.15s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    }

    .loader-spinner-ring--inner {
        inset: 0.65rem;
        border-width: 2px;
        border-color: rgba(59, 130, 246, 0.08);
        border-bottom-color: #60a5fa;
        border-left-color: rgba(147, 197, 253, 0.45);
        animation: loaderRingSpin 1.65s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite reverse;
    }

    .loader-spinner-glow {
        position: absolute;
        inset: 1.1rem;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.22) 0%, transparent 70%);
        animation: loaderGlowPulse 2s ease-in-out infinite;
    }

    .loader-logo {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: loaderLogoFloat 2.4s ease-in-out infinite;
        filter: drop-shadow(0 8px 24px rgba(15, 23, 42, 0.45));
    }

    .loader-text {
        font-size: clamp(1.75rem, 4vw, 2.25rem);
        font-weight: 900;
        margin-bottom: 0.35rem;
        letter-spacing: -0.025em;
        background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .loader-subtext {
        font-size: 0.8125rem;
        color: #94a3b8;
        font-weight: 500;
        letter-spacing: 0.08em;
    }

    .loader-dots {
        display: inline-flex;
        gap: 0.35rem;
        margin-top: 1rem;
        justify-content: center;
    }

    .loader-dots span {
        width: 0.4rem;
        height: 0.4rem;
        border-radius: 9999px;
        background: #3b82f6;
        animation: loaderDotBounce 1.2s ease-in-out infinite;
    }

    .loader-dots span:nth-child(2) { animation-delay: 0.15s; }
    .loader-dots span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes loaderRingSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes loaderGlowPulse {
        0%, 100% { opacity: 0.45; transform: scale(0.96); }
        50% { opacity: 1; transform: scale(1); }
    }

    @keyframes loaderLogoFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    @keyframes loaderDotBounce {
        0%, 80%, 100% { transform: translateY(0); opacity: 0.45; }
        40% { transform: translateY(-5px); opacity: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .loader-spinner-ring,
        .loader-spinner-ring--inner,
        .loader-spinner-glow,
        .loader-logo,
        .loader-dots span {
            animation: none;
        }
    }
</style>
@endonce

<div class="page-loader" id="pageLoader" role="status" aria-live="polite" aria-label="در حال بارگذاری" style="position:fixed;inset:0;z-index:9999;">
    <div class="loader-content">
        <div class="loader-visual">
            <div class="loader-spinner" aria-hidden="true">
                <span class="loader-spinner-glow"></span>
                <span class="loader-spinner-ring"></span>
                <span class="loader-spinner-ring loader-spinner-ring--inner"></span>
            </div>
            <div class="loader-logo">
                <x-brand-logo :size="$logoSize" mode="web" class="{{ $logoClass }} mx-auto" />
            </div>
        </div>
        <div class="loader-text">پارس لیان</div>
        <div class="loader-subtext">تخصصی‌ترین مرکز سخت‌افزار</div>
        <div class="loader-dots" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>
