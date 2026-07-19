<?php

namespace Tests\Feature;

use App\Http\Middleware\TwoFactorMiddleware;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['customer', 'receptionist', 'technician', 'admin', 'super_admin', 'employee'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_in_person_customer_store_updates_existing_record(): void
    {
        $this->withoutMiddleware(TwoFactorMiddleware::class);

        $receptionist = User::factory()->create(['phone' => '09121111111']);
        $receptionist->assignRole('receptionist');

        $existing = Customer::create([
            'name' => 'مشتری قدیمی',
            'phone' => '09122222222',
        ]);

        $response = $this->actingAs($receptionist)->post(route('automation.customers.store'), [
            'name' => 'مشتری بروز شده',
            'phone' => '09122222222',
            'in_person' => '1',
        ]);

        $response->assertRedirect(route('automation.customers.index'));
        $this->assertDatabaseHas('customers', [
            'id' => $existing->id,
            'phone' => '09122222222',
            'name' => 'مشتری بروز شده',
        ]);
    }

    public function test_cannot_attach_customer_phone_to_employee_account(): void
    {
        $this->withoutMiddleware(TwoFactorMiddleware::class);

        $receptionist = User::factory()->create(['phone' => '09121111111']);
        $receptionist->assignRole('receptionist');

        $employee = User::factory()->create(['phone' => '09123333333']);
        $employee->assignRole('technician');

        $response = $this->actingAs($receptionist)->post(route('automation.customers.store'), [
            'name' => 'مشتری جدید',
            'phone' => '09123333333',
            'in_person' => '1',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_store_reuses_soft_deleted_user_with_same_phone(): void
    {
        $this->withoutMiddleware(TwoFactorMiddleware::class);

        $receptionist = User::factory()->create(['phone' => '09125555555']);
        $receptionist->assignRole('receptionist');

        $deletedUser = User::factory()->create(['phone' => '09126666666']);
        $deletedUser->assignRole('customer');
        $deletedUser->delete();

        $response = $this->actingAs($receptionist)->post(route('automation.customers.store'), [
            'name' => 'مشتری بازگشتی',
            'phone' => '09126666666',
        ]);

        $response->assertRedirect(route('automation.customers.index'));
        $response->assertSessionHas('success');

        $this->assertSame(1, User::withTrashed()->where('phone', '09126666666')->count());
        $this->assertDatabaseHas('users', [
            'phone' => '09126666666',
            'name' => 'مشتری بازگشتی',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('customers', [
            'phone' => '09126666666',
            'name' => 'مشتری بازگشتی',
        ]);
    }

    public function test_store_restores_soft_deleted_customer_with_same_phone(): void
    {
        $this->withoutMiddleware(TwoFactorMiddleware::class);

        $receptionist = User::factory()->create(['phone' => '09127777777']);
        $receptionist->assignRole('receptionist');

        $customer = Customer::create([
            'name' => 'مشتری حذف‌شده',
            'phone' => '09128888888',
        ]);
        $customer->delete();

        $response = $this->actingAs($receptionist)->post(route('automation.customers.store'), [
            'name' => 'مشتری بازیابی‌شده',
            'phone' => '09128888888',
        ]);

        $response->assertRedirect(route('automation.customers.index'));
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'phone' => '09128888888',
            'name' => 'مشتری بازیابی‌شده',
            'deleted_at' => null,
        ]);
    }

    public function test_online_register_links_existing_in_person_customer(): void
    {
        Customer::create([
            'name' => 'مشتری حضوری',
            'phone' => '09124444444',
        ]);

        $response = $this->post(route('register'), [
            'first_name' => 'علی',
            'last_name' => 'رضایی',
            'phone' => '09124444444',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $user = User::where('phone', '09124444444')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('customer'));

        $this->assertDatabaseHas('customers', [
            'phone' => '09124444444',
            'user_id' => $user->id,
        ]);
    }
}
