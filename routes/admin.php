<?php

use App\Http\Controllers\SMSManagementController;
use Illuminate\Support\Facades\Route;

$panelMiddleware = ['auth', 'session.limit', 'throttle:web', 'security.headers'];
$productPanelRoles = 'role:admin,super_admin,warehouse,receptionist,accountant';

Route::prefix('panel')->name('admin.')->middleware([...$panelMiddleware, $productPanelRoles])->group(function () {
    Route::get('products/export', [App\Http\Controllers\Admin\ProductManagementController::class, 'export'])->name('products.export');
    Route::post('products/{product}/mark-out-of-stock', [App\Http\Controllers\Admin\ProductManagementController::class, 'markOutOfStock'])->name('products.mark-out-of-stock');
    Route::post('products/{product}/sync-inventory', [App\Http\Controllers\Admin\ProductManagementController::class, 'syncInventory'])->name('products.sync-inventory');
    Route::post('products/{product}/detach-inventory', [App\Http\Controllers\Admin\ProductManagementController::class, 'detachInventory'])->name('products.detach-inventory');
    Route::resource('products', App\Http\Controllers\Admin\ProductManagementController::class);
    Route::post('products/{id}/restore', [App\Http\Controllers\Admin\ProductManagementController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [App\Http\Controllers\Admin\ProductManagementController::class, 'forceDelete'])->name('products.force-delete');
    Route::get('products/{product}/history', [App\Http\Controllers\Admin\ProductManagementController::class, 'history'])->name('products.history');

    Route::resource('categories', App\Http\Controllers\Admin\ProductCategoryController::class);
    Route::post('categories/{id}/restore', [App\Http\Controllers\Admin\ProductCategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{id}/force-delete', [App\Http\Controllers\Admin\ProductCategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::post('categories/store-quick', [App\Http\Controllers\Admin\ProductCategoryController::class, 'storeQuick'])->name('categories.store-quick');
});

// Admin Routes (for regular admins)
Route::prefix('panel')->name('admin.')->middleware([...$panelMiddleware, 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/sms', [\App\Http\Controllers\SettingController::class, 'updateSmsTemplates'])->name('settings.update-sms');
    Route::put('/settings/security', [\App\Http\Controllers\SettingController::class, 'updateSecurity'])->name('settings.update-security');
    Route::put('/settings/service', [\App\Http\Controllers\SettingController::class, 'updateService'])->name('settings.update-service');
    Route::put('/settings/payment-gateways', [\App\Http\Controllers\SettingController::class, 'updatePaymentGateways'])->name('settings.update-payment-gateways');
    Route::post('/settings/licenses', [\App\Http\Controllers\SettingController::class, 'addLicense'])->name('settings.add-license');
    Route::delete('/settings/licenses/{index}', [\App\Http\Controllers\SettingController::class, 'removeLicense'])->name('settings.remove-license');
    Route::put('/settings/public-pages', [\App\Http\Controllers\SettingController::class, 'updatePublicPages'])->name('settings.update-public-pages');

    // مدیریت سطوح دسترسی پیشرفته
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('files', [\App\Http\Controllers\Admin\FileBrowserController::class, 'index'])->name('files.index');
    Route::get('files/download', [\App\Http\Controllers\Admin\FileBrowserController::class, 'download'])->name('files.download');
    Route::delete('files/item', [\App\Http\Controllers\Admin\FileBrowserController::class, 'destroy'])->name('files.destroy');
    
    Route::get('recycle-bin', [\App\Http\Controllers\Admin\RecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::post('recycle-bin/restore/{type}/{id}', [\App\Http\Controllers\Admin\RecycleBinController::class, 'restore'])->name('recycle-bin.restore');
    Route::delete('recycle-bin/force-delete/{type}/{id}', [\App\Http\Controllers\Admin\RecycleBinController::class, 'forceDelete'])->name('recycle-bin.force-delete');

    // Additional admin routes can be added here
    // Note: User management is restricted to super_admin as per requirement
});

// Super Admin Routes (Hidden - accessible only via direct authentication)
Route::middleware(['auth', 'session.limit', 'throttle:web', 'security.headers', 'role:super_admin'])->group(function () {
    // Super admin dashboard - accessible only for super_admin role
    Route::get('/system-admin', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('super-admin.dashboard');

    // Super admin user management - hidden routes
    Route::prefix('system-admin/users')->name('super-admin.users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('index');

        // Explicitly defining create and store routes for super-admin users
        Route::get('/create', [App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('store');

        Route::get('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('show');

        Route::get('/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('edit');

        Route::put('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('update');

        Route::post('/{user}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/restore', [App\Http\Controllers\Admin\UserManagementController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Admin\UserManagementController::class, 'forceDelete'])->name('force-delete');
        Route::delete('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('destroy');
    });
});

// Routes مدیریت SMS - محدود به مدیران
Route::prefix('admin/sms')->name('admin.sms.')->middleware(['auth', 'session.limit', 'throttle:web', 'security.headers', 'role:admin,super_admin,receptionist'])->group(function () {
    Route::get('/dashboard', [SMSManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/logs', [SMSManagementController::class, 'logs'])->name('logs');
    Route::post('/send-test', [SMSManagementController::class, 'sendTestSMS'])->name('send-test');
    Route::get('/balance', [SMSManagementController::class, 'getBalance'])->name('balance');
    Route::get('/stats', [SMSManagementController::class, 'getStats'])->name('stats');

    // مسیرهای پکیج SMS.ir که بصورت امن بازتعریف شده‌اند
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::get('/bulk', [\Cryptommer\Smsir\Controllers\ViewController::class, 'SendBulk'])->name('bulk');
        Route::post('/bulk', [\Cryptommer\Smsir\Controllers\SendController::class, 'SendBulk']);
        Route::get('/report/received/today', [\Cryptommer\Smsir\Controllers\ViewController::class, 'Todayreceived'])->name('report.received.today');
        Route::get('/report/sent/today', [\Cryptommer\Smsir\Controllers\ViewController::class, 'TodaySent'])->name('report.sent.today');
    });
});
