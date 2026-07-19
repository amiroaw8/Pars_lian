<?php

namespace Database\Factories;

use App\Models\AccountingService;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountingServiceFactory extends Factory
{
    protected $model = AccountingService::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 50000, 500000),
            'description' => $this->faker->sentence,
            'service_order_id' => ServiceOrder::factory(),
            'technician_id' => User::factory(),
            'transaction_date' => now(),
            'payment_status' => 'paid',
            'tax_amount' => $this->faker->randomFloat(2, 0, 50000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
