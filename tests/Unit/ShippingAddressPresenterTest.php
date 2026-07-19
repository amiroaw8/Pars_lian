<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Support\ShippingAddressPresenter;
use PHPUnit\Framework\TestCase;

class ShippingAddressPresenterTest extends TestCase
{
    public function test_pickup_pos_order_with_customer_address_shows_only_address(): void
    {
        $order = new Order([
            'shipping_method' => 'pickup',
            'shipping_address' => 'خیابان انقلاب',
            'shipping_city' => 'نامشخص',
            'shipping_state' => null,
            'shipping_postal_code' => null,
        ]);

        $presenter = ShippingAddressPresenter::for($order);

        $this->assertSame(['خیابان انقلاب'], $presenter->lines());
        $this->assertNull($presenter->postalCode());
    }

    public function test_pickup_without_address_shows_store_pickup_label(): void
    {
        $order = new Order([
            'shipping_method' => 'pickup',
            'shipping_address' => 'تحویل حضوری',
            'shipping_city' => 'نامشخص',
        ]);

        $presenter = ShippingAddressPresenter::for($order);

        $this->assertSame(['تحویل حضوری از فروشگاه'], $presenter->lines());
    }

    public function test_online_order_formats_location_street_and_postal_code(): void
    {
        $order = new Order([
            'shipping_method' => 'tipax',
            'shipping_state' => 'لرستان',
            'shipping_city' => 'خرم‌آباد',
            'shipping_address' => 'خیابان انقلاب، پلاک ۱۲',
            'shipping_postal_code' => '6815743361',
        ]);

        $presenter = ShippingAddressPresenter::for($order);

        $this->assertSame([
            'لرستان، خرم‌آباد',
            'خیابان انقلاب، پلاک ۱۲',
        ], $presenter->lines());
        $this->assertSame('6815743361', $presenter->postalCode());
    }
}
