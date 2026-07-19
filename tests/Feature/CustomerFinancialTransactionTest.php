<?php

namespace Tests\Feature;

use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\CustomerFinancialHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerFinancialTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_payment_keeps_debt_row_and_adds_payment_row(): void
    {
        Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);

        $staff = User::factory()->create();
        $staff->assignRole('receptionist');

        $customer = Customer::factory()->create();
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'debt_amount' => 38720000,
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'technician_id' => $serviceOrder->technician_id,
            'amount' => 38720000,
            'description' => '[بدهی] هزینه تعمیر',
            'transaction_date' => now()->subDay(),
            'payment_status' => 'unpaid',
        ]);

        $history = app(CustomerFinancialHistory::class);
        $before = $history->summary($customer);
        $this->assertSame(38720000.0, $before['total_debt']);
        $this->assertSame(0.0, $before['total_paid']);

        $response = $this->actingAs($staff)->post(route('automation.customers.financial-transaction.store', $customer), [
            'category' => 'service',
            'record_type' => 'payment',
            'amount' => 38720000,
            'description' => 'تسویه بدهی سفارش تعمیر',
            'service_order_id' => $serviceOrder->id,
            'confirm' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $serviceOrder->refresh();
        $this->assertSame(0.0, (float) $serviceOrder->debt_amount);

        $timeline = $history->timeline($customer);
        $this->assertCount(1, $timeline->where('type', 'debt'));
        $this->assertCount(1, $timeline->where('type', 'payment'));

        $after = $history->summary($customer);
        $this->assertSame(0.0, $after['total_debt']);
        $this->assertSame(38720000.0, $after['total_paid']);
    }

    public function test_payment_prefix_classifies_record_even_when_description_mentions_debt(): void
    {
        $customer = Customer::factory()->create();
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'debt_amount' => 0,
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'technician_id' => $serviceOrder->technician_id,
            'amount' => 5000000,
            'description' => '[پرداخت] تسویه بدهی',
            'transaction_date' => now(),
            'payment_status' => 'paid',
        ]);

        $summary = app(CustomerFinancialHistory::class)->summary($customer);

        $this->assertSame(5000000.0, $summary['total_paid']);
        $this->assertSame(0.0, $summary['total_debt']);
    }

    public function test_settled_pos_debt_shows_debt_and_inferred_payment_rows(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'payment_method' => 'debt',
            'payment_status' => 'paid',
            'total' => 70000000,
        ]);

        AccountingSale::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => 70000000,
            'description' => 'فروش حضوری (سفارش #ORD-TEST) — بدهی',
            'transaction_date' => now()->subDay(),
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $timeline = app(CustomerFinancialHistory::class)->timeline($customer);

        $this->assertCount(1, $timeline->where('type', 'debt'));
        $this->assertCount(1, $timeline->where('type', 'payment'));

        $summary = app(CustomerFinancialHistory::class)->summary($customer);
        $this->assertSame(70000000.0, $summary['total_paid']);
        $this->assertSame(0.0, $summary['total_debt']);
    }

    public function test_automatic_internal_service_accounting_is_excluded_from_customer_ledger(): void
    {
        $customer = Customer::factory()->create();
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'debt_amount' => 0,
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'technician_id' => $serviceOrder->technician_id,
            'amount' => 38720000,
            'description' => 'خدمات تعمیرات خودکار بابت سفارش تعمیر شماره SRV-00009',
            'transaction_date' => now(),
            'payment_status' => 'paid',
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'technician_id' => $serviceOrder->technician_id,
            'amount' => 38720000,
            'description' => '[پرداخت] تسویه بدهی',
            'transaction_date' => now(),
            'payment_status' => 'paid',
        ]);

        $history = app(CustomerFinancialHistory::class);
        $summary = $history->summary($customer);

        $this->assertSame(38720000.0, $summary['total_paid']);
        $transactions = $history->transactions($customer);
        $this->assertCount(2, $transactions);
        $this->assertCount(1, $transactions->where('type', 'payment'));
        $this->assertCount(1, $transactions->where('type', 'debt'));
    }

    public function test_sales_payment_can_be_linked_to_shop_order(): void
    {
        Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);

        $staff = User::factory()->create();
        $staff->assignRole('receptionist');

        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $staff->id,
            'shipping_phone' => $customer->phone,
            'payment_method' => 'debt',
            'payment_status' => 'pending',
            'total' => 5000000,
        ]);

        AccountingSale::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => 5000000,
            'description' => '[بدهی] فروش حضوری',
            'transaction_date' => now()->subDay(),
            'payment_method' => 'debt',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($staff)->post(route('automation.customers.financial-transaction.store', $customer), [
            'category' => 'sales',
            'record_type' => 'payment',
            'amount' => 5000000,
            'order_id' => $order->id,
            'confirm' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('paid', $order->payment_status->value);
        $this->assertTrue(
            AccountingSale::where('order_id', $order->id)
                ->where('status', 'completed')
                ->where('description', 'like', '%[پرداخت]%')
                ->exists()
        );
    }

    public function test_order_statuses_reflect_service_and_shop_orders(): void
    {
        $customer = Customer::factory()->create();

        $paidService = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'service_cost' => 1000000,
            'debt_amount' => 0,
        ]);

        $debtService = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'service_cost' => 2000000,
            'debt_amount' => 2000000,
        ]);

        $paidShop = Order::factory()->create([
            'shipping_phone' => $customer->phone,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'total' => 3000000,
        ]);

        $debtShop = Order::factory()->create([
            'shipping_phone' => $customer->phone,
            'payment_status' => 'pending',
            'payment_method' => 'debt',
            'total' => 4000000,
        ]);

        $statuses = app(CustomerFinancialHistory::class)->orderStatuses($customer);

        $this->assertSame('paid', $statuses->firstWhere('reference', 'سفارش تعمیر #'.$paidService->id)['payment_status']);
        $this->assertSame('debt', $statuses->firstWhere('reference', 'سفارش تعمیر #'.$debtService->id)['payment_status']);
        $this->assertSame('paid', $statuses->firstWhere('reference', $paidShop->order_number ?: ('سفارش #'.$paidShop->id))['payment_status']);
        $this->assertSame('debt', $statuses->firstWhere('reference', $debtShop->order_number ?: ('سفارش #'.$debtShop->id))['payment_status']);
    }

    public function test_transactions_excludes_proforma_entries(): void
    {
        $customer = Customer::factory()->create();

        AccountingSale::create([
            'customer_id' => $customer->id,
            'amount' => 1000000,
            'description' => '[پیش‌فاکتور] فاکتور موقت',
            'transaction_date' => now(),
            'status' => 'pending',
        ]);

        AccountingSale::create([
            'customer_id' => $customer->id,
            'amount' => 500000,
            'description' => '[پرداخت] دریافت نقدی',
            'transaction_date' => now(),
            'status' => 'completed',
        ]);

        $transactions = app(CustomerFinancialHistory::class)->transactions($customer);

        $this->assertCount(1, $transactions);
        $this->assertSame('payment', $transactions->first()['type']);
    }

    public function test_transactions_include_inferred_debt_when_only_payment_record_exists(): void
    {
        $customer = Customer::factory()->create();
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'service_cost' => 31000000,
            'debt_amount' => 0,
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'technician_id' => $serviceOrder->technician_id,
            'amount' => 31000000,
            'description' => '[پرداخت] سفارش تعمیر',
            'transaction_date' => now(),
            'payment_status' => 'paid',
        ]);

        $transactions = app(CustomerFinancialHistory::class)->transactions($customer);

        $this->assertCount(2, $transactions);
        $this->assertCount(1, $transactions->where('type', 'debt'));
        $this->assertCount(1, $transactions->where('type', 'payment'));
    }
}
