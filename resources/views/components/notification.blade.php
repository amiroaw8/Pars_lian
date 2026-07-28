@props([
    'type' => 'info',
    'title' => null,
    'message' => '',
    'icon' => null,
    'duration' => 5000,
    'closable' => true,
    'actions' => null
])

<?php
    $type = $type ?? 'info';
    $duration = $duration ?? 5000;

    $typeClasses = [
        'success' => [
            'bg' => 'bg-gradient-to-r from-green-500 to-emerald-600',
            'border' => 'border-green-400/30',
            'icon' => 'ti-circle-check',
            'text' => 'text-white'
        ],
        'error' => [
            'bg' => 'bg-gradient-to-r from-red-500 to-rose-600',
            'border' => 'border-red-400/30',
            'icon' => 'ti-alert-circle',
            'text' => 'text-white'
        ],
        'warning' => [
            'bg' => 'bg-gradient-to-r from-yellow-500 to-orange-600',
            'border' => 'border-yellow-400/30',
            'icon' => 'ti-alert-triangle',
            'text' => 'text-white'
        ],
        'info' => [
            'bg' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
            'border' => 'border-blue-400/30',
            'icon' => 'ti-info-circle',
            'text' => 'text-white'
        ]
    ];

    $typeKey = is_string($type) ? $type : 'info';
    $config = $typeClasses[$typeKey] ?? $typeClasses['info'];
    $finalIcon = $icon ?? $config['icon'];
?>

<div
    class="enhanced-notification fixed transition-all duration-500 transform -translate-x-full opacity-0"
    data-duration="{{ $duration }}"
    style="top: 100px; left: 1rem; min-width: 320px; max-width: 480px; z-index: 100000;"
>
    <div class="{{ $config['bg'] }} {{ $config['border'] }} rounded-2xl shadow-2xl border backdrop-blur-sm overflow-hidden">
        <!-- Progress Bar -->
        <div class="notification-progress absolute top-0 left-0 w-full h-1 bg-white/30 transition-all duration-100 ease-linear"
             {!! 'style="transform-origin: left; transform: scaleX(1); animation: shrink ' . $duration . 'ms linear forwards;"' !!}></div>

        <div class="p-4">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shadow-lg">
                    <i class="ti {{ $finalIcon }} text-lg"></i>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    @if($title)
                        <h4 class="font-bold text-sm mb-1 {{ $config['text'] }}">
                            {{ $title }}

                        </h4>
                    @endif
                    <p class="text-sm {{ $config['text'] }} opacity-90 leading-relaxed">
                        {{ $message }}

                    </p>

                    <!-- Actions -->
                    @if($actions)
                        <div class="mt-3 flex gap-2">
                            {{ $actions }}

                        </div>
                    @endif
                </div>

                <!-- Close Button -->
                @if($closable)
                    <button
                        class="notification-close flex-shrink-0 w-6 h-6 rounded-lg bg-white/20 hover:bg-white/30 text-white/80 hover:text-white transition-all duration-200 flex items-center justify-center hover:scale-110"
                        onclick="closeNotification(this)" aria-label="بستن اعلان"
                    >
                        <i class="ti ti-x text-xs"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-3 left-3 w-1 h-1 bg-white/40 rounded-full animate-ping"></div>
        <div class="absolute bottom-3 right-3 w-1 h-1 bg-white/40 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
    </div>
</div>

<style>
    @keyframes shrink {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }

    .enhanced-notification {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.05);
    }

    .enhanced-notification:hover {
        transform: translateX(-4px) scale(1.02);
    }

    /* Stack notifications */
    .enhanced-notification:nth-child(1) { top: 100px; }
    .enhanced-notification:nth-child(2) { top: 180px; }
    .enhanced-notification:nth-child(3) { top: 260px; }
    .enhanced-notification:nth-child(4) { top: 340px; }
    .enhanced-notification:nth-child(5) { top: 420px; }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .enhanced-notification {
            left: 1rem;
            right: 1rem;
            min-width: auto;
            max-width: none;
        }

        .enhanced-notification:nth-child(1) { top: 100px; }
        .enhanced-notification:nth-child(2) { top: 180px; }
        .enhanced-notification:nth-child(3) { top: 260px; }
        .enhanced-notification:nth-child(4) { top: 340px; }
        .enhanced-notification:nth-child(5) { top: 420px; }
    }

    /* Accessibility */
    .notification-close:focus {
        outline: 2px solid rgba(255, 255, 255, 0.8);
        outline-offset: 2px;
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .enhanced-notification {
            transition: none !important;
            animation: none !important;
        }

        .notification-progress {
            display: none;
        }
    }
</style>

<script>
    let notificationQueue = [];
    let activeNotifications = 0;
    const maxNotifications = 5;

    // Enhanced notification system
    window.showNotification = function(options = {}) {
        const {
            type = 'info',
            title = null,
            message = '',
            icon = null,
            duration = 5000,
            closable = true,
            actions = null
        } = options;

        // Limit concurrent notifications
        if (activeNotifications >= maxNotifications) {
            notificationQueue.push(options);
            return;
        }

        activeNotifications++;

        // Create notification element
        const notification = document.createElement('div');
        notification.innerHTML = `
            <div class="enhanced-notification fixed z-[100000] transition-all duration-500 transform -translate-x-full opacity-0"
                 data-duration="${duration}"
                 style="top: ${100 + (activeNotifications - 1) * 80}px; left: 1rem; min-width: 320px; max-width: 480px;">
                <div class="${getNotificationClasses(type)} rounded-2xl shadow-2xl border backdrop-blur-sm overflow-hidden">
                    <div class="notification-progress absolute top-0 left-0 w-full h-1 bg-white/30 transition-all duration-100 ease-linear"
                         style="transform-origin: left; transform: scaleX(1); animation: shrink ${duration}ms linear forwards;"></div>
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shadow-lg">
                                <i class="ti ${icon || getDefaultIcon(type)} text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                ${title ? `<h4 class="font-bold text-sm mb-1 text-white">${title}</h4>` : ''}
                                <p class="text-sm text-white opacity-90 leading-relaxed">${message}</p>
                                ${actions ? `<div class="mt-3 flex gap-2">${actions}</div>` : ''}
                            </div>
                            ${closable ? `
                                <button class="notification-close flex-shrink-0 w-6 h-6 rounded-lg bg-white/20 hover:bg-white/30 text-white/80 hover:text-white transition-all duration-200 flex items-center justify-center hover:scale-110"
                                        onclick="closeNotification(this)" aria-label="بستن اعلان">
                                    <i class="ti ti-x text-xs"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <div class="absolute top-3 left-3 w-1 h-1 bg-white/40 rounded-full animate-ping"></div>
                    <div class="absolute bottom-3 right-3 w-1 h-1 bg-white/40 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            const notifElement = notification.querySelector('.enhanced-notification');
            notifElement.classList.remove('-translate-x-full', 'opacity-0');
        }, 10);

        // Auto close
        const timeout = setTimeout(() => {
            closeNotification(notification.querySelector('.notification-close') || notification);
        }, duration);

        // Store timeout for cleanup
        notification.dismissTimeout = timeout;

        // Add sound effect (optional)
        if ('vibrate' in navigator && type === 'error') {
            navigator.vibrate(200);
        }
    };

    function closeNotification(button) {
        const notification = button.closest('.enhanced-notification') || button;
        const container = notification.parentElement;

        if (notification.dismissTimeout) {
            clearTimeout(notification.dismissTimeout);
        }

        // Animate out
        notification.classList.add('-translate-x-full', 'opacity-0');

        setTimeout(() => {
            if (container && container.parentElement) {
                container.parentElement.removeChild(container);
            }
            activeNotifications--;

            // Show next notification from queue
            if (notificationQueue.length > 0 && activeNotifications < maxNotifications) {
                const nextOptions = notificationQueue.shift();
                window.showNotification(nextOptions);
            }

            // Reposition remaining notifications
            repositionNotifications();
        }, 500);
    }

    function repositionNotifications() {
        const notifications = document.querySelectorAll('.enhanced-notification');
        notifications.forEach((notif, index) => {
            notif.style.top = `${100 + index * 80}px`;
        });
    }

    function getNotificationClasses(type) {
        const classes = {
            success: 'bg-gradient-to-r from-green-500 to-emerald-600 border-green-400/30',
            error: 'bg-gradient-to-r from-red-500 to-rose-600 border-red-400/30',
            warning: 'bg-gradient-to-r from-yellow-500 to-orange-600 border-yellow-400/30',
            info: 'bg-gradient-to-r from-blue-500 to-indigo-600 border-blue-400/30'
        };
        return classes[type] || classes.info;
    }

    function getDefaultIcon(type) {
        const icons = {
            success: 'circle-check',
            error: 'alert-circle',
            warning: 'alert-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Global notification functions
    window.notifySuccess = (message, title = null) => window.showNotification({ type: 'success', title, message });
    window.notifyError = (message, title = null) => window.showNotification({ type: 'error', title, message });
    window.notifyWarning = (message, title = null) => window.showNotification({ type: 'warning', title, message });
    window.notifyInfo = (message, title = null) => window.showNotification({ type: 'info', title, message });
</script>
