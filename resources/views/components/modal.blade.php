@props([
    'size' => 'md',
    'variant' => 'default',
    'backdrop' => 'blur',
    'animation' => 'scale'
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-6xl',
        'full' => 'max-w-full mx-4'
    ];

    $variantClasses = [
        'default' => 'bg-white/95 backdrop-blur-xl border-white/20',
        'glass' => 'bg-white/10 backdrop-blur-2xl border-white/30',
        'dark' => 'bg-gray-900/95 backdrop-blur-xl border-gray-700/50'
    ];

    $backdropClasses = [
        'blur' => 'backdrop-blur-sm bg-black/20',
        'dark' => 'bg-black/50',
        'none' => 'bg-black/0'
    ];

    $animationClasses = [
        'scale' => 'scale-95 opacity-0',
        'slide-up' => 'translate-y-4 opacity-0',
        'slide-down' => '-translate-y-4 opacity-0',
        'fade' => 'opacity-0'
    ];
@endphp

<!-- Enhanced Modal -->
<div
    class="enhanced-modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 transition-all duration-300 {{ $backdropClasses[$backdrop] }}"
    x-data="{ open: false }"
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="open = false"
    style="display: none;"
    x-show="open"
>
    <!-- Modal Container -->
    <div
        class="enhanced-modal-container {{ $sizeClasses[$size] }} w-full {{ $variantClasses[$variant] }} rounded-3xl shadow-2xl border overflow-hidden transform transition-all duration-300 {{ $animationClasses[$animation] }}"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="{{ $animationClasses[$animation] }}"
        x-transition:enter-end="scale-100 opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="scale-100 opacity-100 translate-y-0"
        x-transition:leave-end="{{ $animationClasses[$animation] }}"
        @click.outside="open = false"
    >
        <!-- Modal Glow Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-purple-500/5 to-pink-500/5 rounded-3xl"></div>

        @if($title)
            <!-- Modal Header -->
            <div class="relative modal-header p-6 pb-4 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="modal-title text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center gap-3">
                        @if(isset($icon))
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center shadow-lg">
                                <i class="ti ti-{{ $icon }} text-white text-lg"></i>
                            </div>
                        @endif
                        {{ $title }}
                    </h3>
                    <button
                        class="modal-close w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition-all duration-200 flex items-center justify-center hover:scale-110"
                        @click="open = false"
                    >
                        <i class="ti ti-x text-lg"></i>
            </button>
        </div>
            </div>
        @endif
        
        <!-- Modal Body -->
        <div class="relative modal-body p-6 {{ $title ? 'pt-4' : '' }} max-h-96 overflow-y-auto">
            {{ $slot }}

            <!-- Body decorative elements -->
            <div class="absolute top-4 right-4 w-2 h-2 bg-current opacity-10 rounded-full animate-pulse"></div>
            <div class="absolute bottom-4 left-4 w-1 h-1 bg-current opacity-10 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>
        
        @if(isset($footer))
            <!-- Modal Footer -->
            <div class="relative modal-footer p-6 pt-4 border-t border-white/10 bg-gradient-to-r from-transparent to-white/5">
                <div class="flex items-center justify-end gap-3">
            {{ $footer }}
                </div>
        </div>
        @endif

        <!-- Modal decorative elements -->
        <div class="absolute top-6 left-6 w-4 h-4 bg-blue-400/10 rounded-full animate-ping"></div>
        <div class="absolute bottom-6 right-6 w-3 h-3 bg-purple-400/10 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
    </div>
</div>

<style>
    .enhanced-modal-overlay {
        backdrop-filter: blur(4px);
    }

    .enhanced-modal-container {
        position: relative;
        z-index: 10;
    }

    /* Custom scrollbar for modal body */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.6), rgba(139, 92, 246, 0.6));
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.8), rgba(139, 92, 246, 0.8));
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .enhanced-modal-container {
            margin: 1rem;
            max-height: 90vh;
        }

        .modal-body {
            max-height: 60vh;
            padding: 1rem;
        }

        .modal-header {
            padding: 1rem 1rem 0.5rem;
        }

        .modal-footer {
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
        }
    }

    /* Animation variants */
    .enhanced-modal-container[data-animation="slide-up"] {
        transform: translateY(20px);
    }

    .enhanced-modal-container[data-animation="slide-down"] {
        transform: translateY(-20px);
    }

    /* Accessibility */
    .enhanced-modal-overlay[style*="display: none"] {
        display: none !important;
    }

    /* Focus management */
    .modal-close:focus,
    .modal-body button:focus,
    .modal-footer button:focus {
        outline: 2px solid rgb(59, 130, 246);
        outline-offset: 2px;
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .enhanced-modal-container,
        .enhanced-modal-overlay {
            transition: none !important;
        }
    }
</style>

<script>
    // Modal accessibility improvements
    document.addEventListener('DOMContentLoaded', function() {
        // Focus trap for modals
        function trapFocus(element) {
            const focusableElements = element.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            function handleTab(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            lastElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            firstElement.focus();
                            e.preventDefault();
                        }
                    }
                }
            }

            element.addEventListener('keydown', handleTab);
            firstElement.focus();
        }

        // Auto-focus first focusable element when modal opens
        const modalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const modal = mutation.target;
                    if (modal.style.display !== 'none' && modal.classList.contains('enhanced-modal-overlay')) {
                        const modalContainer = modal.querySelector('.enhanced-modal-container');
                        if (modalContainer) {
                            setTimeout(() => trapFocus(modalContainer), 100);
                        }
                    }
                }
            });
        });

        document.querySelectorAll('.enhanced-modal-overlay').forEach(modal => {
            modalObserver.observe(modal, { attributes: true, attributeFilter: ['style'] });
        });
    });
</script>