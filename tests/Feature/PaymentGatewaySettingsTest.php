<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewaySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_payment_gateway_settings()
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);

        /** @var User $superAdmin */
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $payload = [
            'default' => 'zibal',
            'gateways' => [
                'zarinpal' => [
                    'enabled' => '1',
                    'mode' => 'sandbox',
                    'merchantId' => 'test-zarinpal-merchant',
                    'description' => 'زرین پال سفارشی',
                ],
                'zibal' => [
                    'enabled' => '1',
                    'merchantId' => 'zibal',
                    'description' => 'زیبال سفارشی',
                ],
                'saman' => [
                    'enabled' => '0',
                ],
            ],
        ];

        $response = $this->actingAs($superAdmin)
            ->put(route('admin.settings.update-payment-gateways'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $settings = PaymentGatewayManager::getSettings();
        $this->assertEquals('zibal', $settings['default']);
        $this->assertTrue($settings['gateways']['zarinpal']['enabled']);
        $this->assertEquals('test-zarinpal-merchant', $settings['gateways']['zarinpal']['merchantId']);
        $this->assertTrue($settings['gateways']['zibal']['enabled']);
    }

    public function test_active_gateways_returns_enabled_drivers()
    {
        PaymentGatewayManager::saveSettings([
            'default' => 'zibal',
            'gateways' => [
                'zarinpal' => ['enabled' => '1'],
                'zibal' => ['enabled' => '1'],
                'saman' => ['enabled' => '0'],
            ],
        ]);

        $active = PaymentGatewayManager::getActiveGateways();

        $this->assertArrayHasKey('zarinpal', $active);
        $this->assertArrayHasKey('zibal', $active);
        $this->assertArrayNotHasKey('saman', $active);
    }
}
