<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);

        return [
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'service_type' => fake()->randomElement(['in_company', 'on_site']),
            'receiver_name' => fake()->name(),
            'receiver_phone' => '09'.fake()->numerify('#########'),
            'user_department' => fake()->optional()->word(),
            'accessories' => fake()->optional()->sentence(),
            'fault' => fake()->sentence(),
            'notes' => fake()->optional()->paragraph(),
            'technician_id' => User::factory(),
            'repair_started_at' => fake()->optional()->dateTime(),
            'repair_completed_at' => fake()->optional()->dateTime(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\ServiceOrder $order) {
            if (!isset($order->status)) {
                $order->forceFill(['status' => 'registered']);
            }
            if (!isset($order->service_cost)) {
                $order->forceFill(['service_cost' => fake()->numberBetween(10000, 500000)]);
            }
        });
    }

    /**
     * Service order with registered status
     */
    public function registered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'registered',
            'repair_started_at' => null,
            'repair_completed_at' => null,
        ]);
    }

    /**
     * Service order with repairing status
     */
    public function repairing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'repairing',
            'repair_started_at' => now(),
            'repair_completed_at' => null,
        ]);
    }

    /**
     * Service order with ready status
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ready',
            'repair_started_at' => now()->subDays(2),
            'repair_completed_at' => now()->subDays(1),
        ]);
    }

    /**
     * Service order with delivered status
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'repair_started_at' => now()->subDays(3),
            'repair_completed_at' => now()->subDays(2),
        ]);
    }

    /**
     * Service order without technician
     */
    public function withoutTechnician(): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => null,
        ]);
    }

    /**
     * Service order with specific service type
     */
    public function inCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'in_company',
        ]);
    }

    /**
     * Service order with on-site service
     */
    public function onSite(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'on_site',
        ]);
    }
}
