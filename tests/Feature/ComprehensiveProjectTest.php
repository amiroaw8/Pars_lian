<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Models\ServiceOrder;
use App\Models\Customer;
use App\Models\Device;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use PHPUnit\Framework\Attributes\Test;
use Morilog\Jalali\Jalalian;

class ComprehensiveProjectTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $technician;
    protected $accountant;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Database\Eloquent\Model::unguard();

        // ایجاد نقش‌ها
        $roles = ['admin', 'technician', 'accountant', 'super_admin', 'warehouse', 'receptionist', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->technician = User::factory()->create();
        $this->technician->assignRole('technician');

        $this->accountant = User::factory()->create();
        $this->accountant->assignRole('accountant');
    }

    /**
     * تست تعریف شدن مسیرهای مدیریتی و عدم وجود مسیرهای غیرمجاز
     */
    #[Test]
    public function route_definitions_test()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('super-admin.users.destroy'), 'Route super-admin.users.destroy is not defined');
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.users.index'), 'Route admin.users.index is defined but shouldn\'t be');

        // چک کردن کارکرد هلپر route()
        $url = route('super-admin.users.destroy', ['user' => 1]);
        $this->assertNotEmpty($url);
    }

    /**
     * تست جریان کامل انبارداری: ایجاد، تراکنش، و بررسی موجودی
     */
    #[Test]
    public function inventory_full_workflow_test()
    {
        $inventory = Inventory::create([
            'name' => 'قطعه تست',
            'type' => 'part',
            'quantity' => 10,
            'price' => 50000,
            'description' => 'توضیحات تست'
        ]);

        // افزایش موجودی
        $inventory->updateStock(5, 'purchase', 'خرید قطعات جدید');
        $this->assertEquals(15, $inventory->fresh()->quantity);

        // بررسی تراکنش ثبت شده
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $inventory->id,
            'quantity_change' => 5,
            'transaction_type' => 'purchase',
            'new_quantity' => 15
        ]);

        // کاهش موجودی
        $inventory->updateStock(-3, 'use', 'استفاده در تعمیر');
        $this->assertEquals(12, $inventory->fresh()->quantity);
        $this->assertEquals(12, $inventory->transactions()->latest()->first()->new_quantity);

        // تست تعدیل موجودی (Adjustment)
        $inventory->updateStock(8, 'adjustment', 'اصلاح موجودی انبار');
        $this->assertEquals(20, $inventory->fresh()->quantity);

        // خطای موجودی منفی
        $this->expectException(\RuntimeException::class);
        $inventory->updateStock(-25, 'use', 'تلاش برای استفاده بیش از موجودی');
    }

    /**
     * تست یکپارچگی سفارشات و محصولات شامل کاهش موجودی و لغو سفارش
     */
    #[Test]
    public function order_and_product_integration_test()
    {
        $category = ProductCategory::create([
            'name' => 'دسته تست',
            'slug' => 'test-category',
            'is_active' => true
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'لپ‌تاپ تست',
            'slug' => 'test-laptop',
            'sku' => 'TEST-SKU-123',
            'price' => 1000000,
            'stock_quantity' => 5,
            'manage_stock' => true,
            'is_active' => true
        ]);

        // ایجاد سبد خرید و تبدیل به سفارش
        $cart = \App\Models\Cart::create([
            'user_id' => $this->admin->id,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'subtotal' => 0,
            'total' => 0
        ]);
        $cart->addItem($product->id, 2);
        $cart->refresh(); // اطمینان از بروزرسانی مقادیر در مدل

        $orderData = [
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => 'online',
            'shipping_first_name' => 'تست',
            'shipping_last_name' => 'تستی',
            'shipping_email' => 'test@example.com',
            'shipping_phone' => '09123456789',
            'shipping_address' => 'تهران، خیابان تست',
            'shipping_city' => 'تهران',
            'discount_amount' => 0 // اضافه کردن صریح برای جلوگیری از خطای null
        ];

        $order = $cart->convertToOrder($orderData);

        // بررسی کاهش موجودی محصول
        $this->assertEquals(3, $product->fresh()->stock_quantity);
        $this->assertEquals(1, $order->items()->count());
        $this->assertEquals(2, $order->items()->first()->quantity);

        // تست لغو سفارش و بازگشت موجودی
        $order->cancel();
        $this->assertEquals(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertEquals(5, $product->fresh()->stock_quantity);
    }

    /**
     * تست جریان تعمیرات: تغییر وضعیت، ثبت قطعات و محاسبات هزینه
     */
    #[Test]
    public function service_order_status_workflow_test()
    {
        \Illuminate\Support\Facades\Queue::fake();

        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);

        $serviceOrder = ServiceOrder::unguarded(fn() => ServiceOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'status' => 'registered',
            'service_type' => 'in_company',
            'fault' => 'روشن نمی‌شود',
            'receiver_name' => 'تست گیرنده',
            'receiver_phone' => '09123456789'
        ]));

        // تغییر وضعیت به در حال تعمیر و بررسی ثبت زمان شروع
        // استفاده از کنترلر برای تست واقعی‌تر (در صورت نیاز) یا مستقیماً از مدل
        $serviceOrder->update(['status' => 'repairing', 'technician_id' => $this->technician->id, 'repair_started_at' => now()]);
        $this->assertEquals('repairing', $serviceOrder->fresh()->status->value);

        // ثبت قطعات مصرفی و هزینه خدمات (استفاده از labor به جای service)
        $serviceOrder->repairItems()->createMany([
            [
                'item_type' => 'part',
                'name' => 'مادربرد',
                'cost' => 500000,
                'quantity' => 1
            ],
            [
                'item_type' => 'labor',
                'name' => 'هزینه تعمیر تخصصی',
                'cost' => 200000,
                'quantity' => 1
            ]
        ]);

        // بررسی مجموع هزینه
        $this->assertEquals(700000, $serviceOrder->fresh()->calculated_service_cost);
    }

    /**
     * تست یکپارچگی سیستم پیامک و اطلاع‌رسانی
     */
    #[Test]
    public function sms_integration_and_logging_test()
    {
        \Illuminate\Support\Facades\Bus::fake();
        \Illuminate\Support\Facades\Http::fake();

        // تست ارسال پیامک دستی
        $response = $this->actingAs($this->admin)->post('/automation/sms/send', [
            'phone' => '09123456789',
            'message' => 'پیامک تست جامع'
        ]);

        $response->assertStatus(200);
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SendSmsJob::class);

        // تست ثبت لاگ پیامک (واحدی)
        $smsService = app(\App\Services\SMSService::class);
        $smsService->sendSMS('09123456789', 'تست لاگ');

        $this->assertDatabaseHas('sms_logs', [
            'phone' => '09123456789',
            'message' => 'تست لاگ'
        ]);
    }

    /**
     * تست حریم خصوصی مشتری: مشتری فقط باید سفارشات خود را ببیند
     */
    #[Test]
    public function customer_order_privacy_test()
    {
        $customer1 = User::factory()->create();
        $customer2 = User::factory()->create();

        $order1 = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-101',
            'subtotal' => 1000,
            'total' => 1000,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'shipping_first_name' => 'مشتری ۱',
            'shipping_last_name' => 'تستی',
            'shipping_email' => 'customer1@example.com',
            'shipping_phone' => '09120000001',
            'shipping_address' => 'آدرس ۱',
            'shipping_city' => 'تهران'
        ]);

        $order2 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'ORD-102',
            'subtotal' => 2000,
            'total' => 2000,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'shipping_first_name' => 'مشتری ۲',
            'shipping_last_name' => 'تستی',
            'shipping_email' => 'customer2@example.com',
            'shipping_phone' => '09120000002',
            'shipping_address' => 'آدرس ۲',
            'shipping_city' => 'تهران'
        ]);

        // مشتری ۱ نباید بتواند سفارش مشتری ۲ را ببیند
        /** @var \Illuminate\Contracts\Auth\Authenticatable $customer1 */
        $response = $this->actingAs($customer1)->get(route('customer.orders.show', $order2->id));
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    /**
     * تست حذف پیوست‌ها و امنیت آن
     */
    #[Test]
    public function attachment_deletion_security_test()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $otherUser = User::factory()->create();
        $attachment = \App\Models\Attachment::create([
            'name' => 'test-file.jpg',
            'path' => 'attachments/test-file.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'uploaded_by' => $otherUser->id,
            'attachable_id' => 1,
            'attachable_type' => 'App\Models\ServiceOrder'
        ]);

        // تکنیسین نباید بتواند فایل آپلود شده توسط دیگری را حذف کند
        $response = $this->actingAs($this->technician)
            ->delete(route('automation.attachments.destroy', $attachment->id));

        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);

        // ادمین باید بتواند هر فایلی را حذف کند
        $response = $this->actingAs($this->admin)
            ->delete(route('automation.attachments.destroy', $attachment->id));

        $this->assertSoftDeleted('attachments', ['id' => $attachment->id]);
    }

    /**
     * تست منطق ویرایش سفارش خدمات
     */
    #[Test]
    public function service_order_editing_policy_test()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);

        $serviceOrder = ServiceOrder::unguarded(fn () => ServiceOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'status' => 'delivered',
            'service_type' => 'in_company',
            'receiver_name' => 'تست',
            'receiver_phone' => '09123456789',
            'fault' => 'تست ایراد',
        ]));

        $this->assertFalse($serviceOrder->canBeEdited());

        $serviceOrder->update(['status' => 'repairing']);
        $this->assertTrue($serviceOrder->fresh()->canBeEdited());
    }

    /**
     * تست سلسله مراتب انواع دستگاه
     */
    #[Test]
    public function device_type_hierarchy_test()
    {
        $parent = \App\Models\DeviceType::create(['name' => 'لپ‌تاپ']);
        $child = \App\Models\DeviceType::create(['name' => 'ایسوس', 'parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals('ایسوس', $parent->children->first()->name);
    }

    /**
     * تست صادرات اطلاعات (Export)
     */
    #[Test]
    public function service_order_export_test()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('automation.service-orders.export'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'spreadsheet') ||
            str_contains($response->headers->get('Content-Disposition'), 'attachment'));
    }

    /**
     * تست مدیریت وضعیت سفارشات فروشگاهی توسط مدیر
     */
    #[Test]
    public function order_status_management_test()
    {
        $order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-MGMT-1',
            'subtotal' => 5000,
            'total' => 5000,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'shipping_first_name' => 'تست',
            'shipping_last_name' => 'مدیریت',
            'shipping_email' => 'test@example.com',
            'shipping_phone' => '09123456789',
            'shipping_address' => 'آدرس',
            'shipping_city' => 'تهران'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('automation.orders.update-status', $order->id), [
                'status' => 'processing',
                'payment_status' => 'paid'
            ]);

        $response->assertRedirect();
        $this->assertEquals('processing', $order->fresh()->status->value);
        $this->assertEquals('paid', $order->fresh()->payment_status->value);
    }

    /**
     * تست دسترسی‌های بخش انبار (Inventory Permissions)
     */
    #[Test]
    public function inventory_permission_test()
    {
        // تکنیسین نباید به انبار دسترسی داشته باشد (مگر اینکه صراحتاً در مدل User تعریف شده باشد)
        // طبق مدل User، فقط ادمین و انباردار دسترسی دارند
        $response = $this->actingAs($this->technician)
            ->get(route('automation.inventory.index'));

        $response->assertStatus(403);

        // ادمین باید دسترسی داشته باشد
        $response = $this->actingAs($this->admin)
            ->get(route('automation.inventory.index'));

        $response->assertStatus(200);
    }

    /**
     * تست API انواع دستگاه (DeviceType API)
     */
    #[Test]
    public function device_type_api_test()
    {
        $parent = \App\Models\DeviceType::create(['name' => 'موبایل']);
        \App\Models\DeviceType::create(['name' => 'سامسونگ', 'parent_id' => $parent->id]);

        $response = $this->get("/api/device-types/children/موبایل");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'سامسونگ']);
    }

    /**
     * تست امنیت مدیریت کاربران (User Management Security)
     */
    #[Test]
    public function user_management_security_test()
    {
        /** @var User $superAdmin */
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // ادمین نباید به روت‌های مدیریت کاربران دسترسی داشته باشد (حتی برای حذف)
        $response = $this->actingAs($this->admin)
            ->delete('/system-admin/users/' . $this->admin->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);

        // ادمین نباید به روت‌های مدیریت کاربران دسترسی داشته باشد
        $response = $this->actingAs($this->admin)
            ->delete('/system-admin/users/' . $superAdmin->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    /**
     * تست داشبورد مشتری و فیلتر اطلاعات بر اساس شماره تماس
     */
    #[Test]
    public function customer_dashboard_data_filter_test()
    {
        $customerUser = User::factory()->create(['phone' => '09123456789']);
        $customerRecord = Customer::factory()->create(['phone' => '09123456789']);

        $device = Device::factory()->create([
            'customer_id' => $customerRecord->id,
            'type' => 'گوشی تست',
            'model' => 'مدل تست'
        ]);

        $order = ServiceOrder::unguarded(fn() => ServiceOrder::create([
            'customer_id' => $customerRecord->id,
            'device_id' => $device->id,
            'status' => 'registered',
            'service_type' => 'in_company',
            'receiver_name' => 'تست',
            'receiver_phone' => '09123456789',
            'fault' => 'ایراد تست'
        ]));

        // سفارش مشتری دیگر
        $otherCustomer = Customer::factory()->create(['phone' => '09999999999']);
        $otherDevice = Device::factory()->create([
            'customer_id' => $otherCustomer->id,
            'type' => 'لپ تاپ تست',
            'model' => 'مدل دیگر'
        ]);

        $otherOrder = ServiceOrder::unguarded(fn() => ServiceOrder::create([
            'customer_id' => $otherCustomer->id,
            'device_id' => $otherDevice->id,
            'status' => 'registered',
            'service_type' => 'in_company',
            'receiver_name' => 'تست ۲',
            'receiver_phone' => '09999999999',
            'fault' => 'ایراد تست ۲'
        ]));

        /** @var \Illuminate\Contracts\Auth\Authenticatable $customerUser */
        $response = $this->actingAs($customerUser)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('گوشی تست');
        $response->assertDontSee('لپ تاپ تست');
    }

    /**
     * تست امنیت و سطوح دسترسی پیشرفته (RBAC)
     */
    #[Test]
    public function unauthorized_access_security_test()
    {
        // ۱. کاربری که لاگین نکرده نباید به پنل اتوماسیون دسترسی داشته باشد
        $this->get('/automation/dashboard')->assertRedirect('/login');

        // ۲. تکنیسین نباید به بخش حسابداری دسترسی داشته باشد
        $this->actingAs($this->technician)
            ->get('/automation/accounting')
            ->assertStatus(403);

        // ۳. حسابدار نباید به بخش مدیریت کاربران (پنل ادمین) دسترسی داشته باشد
        $this->actingAs($this->accountant)
            ->get('/system-admin/users')
            ->assertStatus(403);

        // ۴. ادمین باید به همه بخش‌ها دسترسی داشته باشد
        $this->actingAs($this->admin)
            ->get('/automation/accounting')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/automation/inventory')
            ->assertStatus(200);
    }

    /**
     * تست محاسبات دقیق مالی و گزارشات حسابداری
     */
    #[Test]
    public function precise_accounting_calculation_test()
    {
        $amounts = [1250000.75, 3749999.25, 5000000.00];

        foreach ($amounts as $index => $amount) {
            \App\Models\AccountingService::factory()->create([
                'amount' => $amount,
                'description' => 'خدمت ' . ($index + 1),
                'transaction_date' => now()->format('Y-m-d'),
            ]);
        }

        $response = $this->actingAs($this->accountant)->get('/automation/accounting');
        $response->assertSee('10,000,000');

        $today = Jalalian::now()->format('Y/m/d');
        $response = $this->actingAs($this->accountant)
            ->get("/automation/accounting?start_date={$today}&end_date={$today}");
        $response->assertViewHas('totalServices', 10000000);

        $yesterday = Jalalian::now()->subDays(1)->format('Y/m/d');
        $response = $this->actingAs($this->accountant)
            ->get("/automation/accounting?start_date={$yesterday}&end_date={$yesterday}");
        $response->assertViewHas('totalServices', 0);
    }
}
