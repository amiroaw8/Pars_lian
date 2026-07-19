<?php

namespace App\Providers;

use App\View\Components\EnhancedCard;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\View\Components\EnhancedStatusBadge;
use App\View\Components\EnhancedTable;
use Illuminate\Support\Facades\Event;
use App\Events\ServiceOrderCreated;
use App\Models\ServiceOrder;
use App\Policies\ServiceOrderPolicy;
use App\Events\ServiceOrderStatusChanged;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Events\PaymentStatusChanged;
use App\Listeners\SendServiceOrderSms;
use App\Listeners\SendOrderSms;
use App\Listeners\SyncOrderToAccounting;
use App\Listeners\SyncServiceOrderToAccounting;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Policies\DashboardCellPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Support/helpers.php');

        $this->app->bind(
            \App\Repositories\Interfaces\OrderRepositoryInterface::class,
            \App\Repositories\EloquentOrderRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\ServiceOrderRepositoryInterface::class,
            \App\Repositories\EloquentServiceOrderRepository::class
        );

        $this->app->bind(
            \App\Services\Payment\PaymentGatewayInterface::class,
            \App\Services\Payment\Gateways\ZarinpalGateway::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::define('view-repair-cell', [DashboardCellPolicy::class, 'viewRepairCell']);
        Gate::define('view-sales-cell', [DashboardCellPolicy::class, 'viewSalesCell']);
        Gate::define('view-warehouse-cell', [DashboardCellPolicy::class, 'viewWarehouseCell']);
        Gate::define('view-accounting-cell', [DashboardCellPolicy::class, 'viewAccountingCell']);
        Gate::policy(ServiceOrder::class, ServiceOrderPolicy::class);

        \App\Models\ServiceOrder::observe(\App\Observers\ServiceOrderObserver::class);
        \App\Models\Inventory::observe(\App\Observers\InventoryObserver::class);

        Event::listen(
            ServiceOrderCreated::class,
            SendServiceOrderSms::class,
        );

        Event::listen(
            ServiceOrderStatusChanged::class,
            SendServiceOrderSms::class,
        );

        Event::listen(
            ServiceOrderStatusChanged::class,
            SyncServiceOrderToAccounting::class,
        );

        Event::listen(
            OrderCreated::class,
            SendOrderSms::class,
        );

        Event::listen(
            OrderStatusChanged::class,
            SendOrderSms::class,
        );

        Event::listen(
            PaymentStatusChanged::class,
            SyncOrderToAccounting::class,
        );
        
        // Default API Rate Limiter
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Authentication Rate Limiter (Login, Register) — legacy alias
        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });
        // Verification Rate Limiter (2FA Verify) — legacy alias
        \Illuminate\Support\Facades\RateLimiter::for('verify', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Checkout Rate Limiter
        \Illuminate\Support\Facades\RateLimiter::for('checkout', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        // Global Web Rate Limiter
        \Illuminate\Support\Facades\RateLimiter::for('web', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        // SMS Rate Limiter
        \Illuminate\Support\Facades\RateLimiter::for('sms', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
        });

        Blade::component('enhanced-card', EnhancedCard::class);
        Blade::component('enhanced-table', EnhancedTable::class);
        Blade::component('enhanced-status-badge', EnhancedStatusBadge::class);

        // Register Observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }
}
