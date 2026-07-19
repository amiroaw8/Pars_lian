<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('??###')),
            'user_id' => User::factory(),
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'subtotal' => 1000,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total' => 1000,
            'currency' => 'IRT',
            'payment_method' => 'cod',
            'shipping_first_name' => $this->faker->firstName,
            'shipping_last_name' => $this->faker->lastName,
            'shipping_email' => $this->faker->safeEmail,
            'shipping_phone' => $this->faker->phoneNumber,
            'shipping_address' => $this->faker->address,
            'shipping_city' => $this->faker->city,
        ];
    }
}
