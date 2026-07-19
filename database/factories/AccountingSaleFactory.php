<?php

namespace Database\Factories;

use App\Models\AccountingSale;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountingSaleFactory extends Factory
{
    protected $model = AccountingSale::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10000, 1000000),
            'description' => $this->faker->sentence,
            'order_id' => Order::factory(), // Points to shop orders now
            'customer_id' => Customer::factory(),
            'transaction_date' => now(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'bank_transfer']),
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
