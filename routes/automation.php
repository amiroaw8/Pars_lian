<?php

use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\Automation\AccountingExpenseController;
use App\Http\Controllers\Automation\POSController;
use Illuminate\Support\Facades\Route;

// Automation Routes (accessible for authorized staff)
Route::middleware(['auth', 'session.limit', 'throttle:web', 'security.headers', 'role:admin,super_admin,technician,receptionist,warehouse,accountant,employee'])->prefix('automation')->name('automation.')->group(function () {

    Route::get('money/words', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'words' => \App\Support\ShopFormat::amountInWords($request->query('amount', 0)),
        ]);
    })->name('money.words');

    // Dashboard for employees
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'automation'])->name('dashboard');
    Route::get('/dashboard/active-work', [App\Http\Controllers\DashboardController::class, 'activeWorkJson'])->name('dashboard.active-work');

    // Custom Cartables
    Route::get('/dashboard/reception', [App\Http\Controllers\DashboardController::class, 'receptionCartable'])->name('dashboard.reception');
    Route::get('/dashboard/repair', [App\Http\Controllers\DashboardController::class, 'repairCartable'])->name('dashboard.repair');
    Route::get('/dashboard/accounting', [App\Http\Controllers\DashboardController::class, 'accountingCartable'])->name('dashboard.accounting');
    Route::get('/dashboard/bank', [App\Http\Controllers\DashboardController::class, 'deviceBank'])->name('dashboard.bank');

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/search', [CustomerController::class, 'search'])->name('search');
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::post('/{customer}/interactions', [CustomerController::class, 'storeInteraction'])->name('interactions.store');
        Route::post('/{customer}/financial-transaction', [CustomerController::class, 'storeFinancialTransaction'])->name('financial-transaction.store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('force-delete');
    });

    Route::resource('service-orders', ServiceOrderController::class)->except(['destroy']);
    Route::delete('service-orders/{service_order}', [ServiceOrderController::class, 'destroy'])->name('service-orders.destroy');
    Route::post('service-orders/{id}/restore', [ServiceOrderController::class, 'restore'])->name('service-orders.restore');
    Route::delete('service-orders/{id}/force-delete', [ServiceOrderController::class, 'forceDelete'])->name('service-orders.force-delete');
    Route::post('service-orders/{serviceOrder}/assign-technician', [ServiceOrderController::class, 'assignTechnician'])->name('service-orders.assign-technician');
    Route::post('service-orders/{serviceOrder}/assign-self', [ServiceOrderController::class, 'assignSelf'])->name('service-orders.assign-self');
    Route::post('service-orders/{serviceOrder}/attachments', [ServiceOrderController::class, 'storeAttachments'])->name('service-orders.attachments.store');

    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
    Route::get('orders/{order}/print', [App\Http\Controllers\OrderPrintController::class, 'show'])->name('orders.print');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/update-tracking', [OrderController::class, 'updateTracking'])->name('orders.update-tracking');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/notes', [OrderController::class, 'storeNote'])->name('orders.notes.store');
    Route::post('orders/{order}/settle-debt', [OrderController::class, 'settleDebt'])->name('orders.settle-debt');
    Route::get('service-orders-export', [ServiceOrderController::class, 'export'])->name('service-orders.export');
    Route::post('service-orders/{serviceOrder}/start-repair', [ServiceOrderController::class, 'startRepair'])->name('service-orders.start-repair');
    Route::post('service-orders/{serviceOrder}/update-status', [ServiceOrderController::class, 'updateStatus'])->name('service-orders.update-status');

    Route::resource('devices', DeviceController::class);
    Route::post('/devices/{id}/restore', [DeviceController::class, 'restore'])->name('devices.restore');
    Route::delete('/devices/{id}/force-delete', [DeviceController::class, 'forceDelete'])->name('devices.force-delete');

    // Inventory Reports
    Route::get('/inventory/reports', [\App\Http\Controllers\InventoryReportController::class, 'index'])->name('inventory.reports.index');
    Route::get('/inventory/reports/balance', [\App\Http\Controllers\InventoryReportController::class, 'balance'])->name('inventory.reports.balance');
    Route::get('/inventory/reports/balance/export', [\App\Http\Controllers\InventoryReportController::class, 'exportBalance'])->name('inventory.reports.balance.export');
    Route::get('/inventory/reports/cardex', [\App\Http\Controllers\InventoryReportController::class, 'cardex'])->name('inventory.reports.cardex');
    Route::get('/inventory/reports/cardex/export', [\App\Http\Controllers\InventoryReportController::class, 'exportCardex'])->name('inventory.reports.cardex.export');
    Route::get('/inventory/reports/transactions', [\App\Http\Controllers\InventoryReportController::class, 'transactions'])->name('inventory.reports.transactions');
    Route::get('/inventory/reports/transactions/export', [\App\Http\Controllers\InventoryReportController::class, 'exportTransactions'])->name('inventory.reports.transactions.export');

    Route::resource('inventory', InventoryController::class);
    Route::resource('device-types', DeviceTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/device-types/{id}/restore', [DeviceTypeController::class, 'restore'])->name('device-types.restore');
    Route::delete('/device-types/{id}/force-delete', [DeviceTypeController::class, 'forceDelete'])->name('device-types.force-delete');
    Route::get('attachments/{attachment}/preview', [AttachmentController::class, 'preview'])->name('attachments.preview');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Route های مدیریت موجودی
    Route::post('/inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
    Route::delete('/inventory/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.force-delete');
    Route::post('/inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    Route::post('/inventory/{inventory}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::post('/inventory/{inventory}/sync-shop-products', [InventoryController::class, 'syncShopProducts'])->name('inventory.sync-shop-products');

    // Routes حسابداری
    Route::prefix('accounting')->name('accounting.')->middleware('role:admin,accountant')->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        Route::redirect('create-sale', '/automation/pos')->name('create-sale');
        Route::post('store-service', [AccountingController::class, 'storeService'])->name('store-service');
        Route::get('export', [AccountingController::class, 'export'])->name('export');
        Route::get('proforma/create', [ProformaInvoiceController::class, 'create'])->name('proforma.create');
        Route::post('proforma/print', [ProformaInvoiceController::class, 'store'])->name('proforma.print');
        Route::get('proforma/print/{token}', [ProformaInvoiceController::class, 'show'])
            ->where('token', '[A-Za-z0-9]{32,64}')
            ->name('proforma.print.show');
        Route::get('proforma/print', [ProformaInvoiceController::class, 'redirectToCreate'])->name('proforma.print.get');
    
        // Expenses
        Route::resource('expenses', AccountingExpenseController::class)->names([
            'index' => 'expenses.index',
            'create' => 'expenses.create',
            'store' => 'expenses.store',
            'edit' => 'expenses.edit',
            'update' => 'expenses.update',
            'destroy' => 'expenses.destroy',
        ]);
    });

    // Routes تعمیرات (یکپارچه شده با service-orders)
    Route::prefix('repairs')->name('repairs.')->group(function () {
        Route::get('/', [ServiceOrderController::class, 'index'])->name('index');
        Route::get('/{serviceOrder}', [ServiceOrderController::class, 'show'])->name('show');
        Route::get('/{serviceOrder}/edit', [ServiceOrderController::class, 'edit'])->name('edit');
        Route::get('/{serviceOrder}/print', [App\Http\Controllers\RepairPrintController::class, 'show'])->name('print');
        Route::get('/{serviceOrder}/print-sheet', [App\Http\Controllers\RepairPrintController::class, 'printSheet'])->name('print-sheet');
        Route::get('/{serviceOrder}/proforma', [App\Http\Controllers\RepairPrintController::class, 'proformaCreate'])->name('proforma.create');
        Route::post('/{serviceOrder}/proforma/print', [App\Http\Controllers\RepairPrintController::class, 'proformaStore'])->name('proforma.print');
        Route::get('/{serviceOrder}/proforma/print/{token}', [App\Http\Controllers\RepairPrintController::class, 'proformaPrintShow'])->name('proforma.print.show');
        Route::put('/{serviceOrder}/update', [ServiceOrderController::class, 'updateRepair'])->name('update');
        Route::post('/{serviceOrder}/start', [ServiceOrderController::class, 'startRepair'])->name('start');
        Route::post('/{serviceOrder}/complete', [ServiceOrderController::class, 'completeRepair'])->name('complete');
        Route::post('/{serviceOrder}/add-item', [ServiceOrderController::class, 'addRepairItem'])->name('add-item');
        Route::put('/{serviceOrder}/update-item/{repairItem}', [ServiceOrderController::class, 'updateRepairItem'])->name('update-item');
        Route::delete('/{serviceOrder}/remove-item/{repairItem}', [ServiceOrderController::class, 'removeRepairItem'])->name('remove-item');
        Route::post('/{serviceOrder}/assign-technician', [ServiceOrderController::class, 'assignTechnician'])->name('assign-technician');
        Route::post('/{serviceOrder}/assign-self', [ServiceOrderController::class, 'assignSelf'])->name('assign-self');
        Route::post('/{serviceOrder}/update-costs', [ServiceOrderController::class, 'updateCosts'])->name('update-costs');
        Route::post('/{serviceOrder}/reject', [ServiceOrderController::class, 'reject'])->name('reject');
        Route::post('/{serviceOrder}/verify-payment', [ServiceOrderController::class, 'verifyPayment'])->name('verify-payment');
        Route::post('/{serviceOrder}/record-debt', [ServiceOrderController::class, 'recordDebt'])->name('record-debt');
        Route::post('/{serviceOrder}/settle-debt', [ServiceOrderController::class, 'settleDebt'])->name('settle-debt');
        Route::post('/{serviceOrder}/reorder-items', [ServiceOrderController::class, 'reorderRepairItems'])->name('reorder-items');
        Route::post('/{serviceOrder}/deliver', [ServiceOrderController::class, 'deliver'])->name('deliver');
        Route::get('/{serviceOrder}/export-actions', [ServiceOrderController::class, 'exportActions'])->name('export-actions');
        Route::post('/{serviceOrder}/archive', [ServiceOrderController::class, 'archive'])->name('archive');
    });

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/sales', [POSController::class, 'sales'])->name('sales');
        Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');
        Route::get('/search', [POSController::class, 'searchProducts'])->name('search');
    });

    // Routes SMS
    Route::prefix('sms')->name('sms.')->middleware('throttle:sms')->group(function () {
        Route::post('/send', [SMSController::class, 'sendSMS'])->name('send');
        Route::get('/logs', [SMSController::class, 'getLogs'])->name('logs');
        Route::get('/status/{smsId}', [SMSController::class, 'getStatus'])->name('status');
    });
});
