<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => fake()->randomElement(['Laptop', 'Desktop', 'Printer', 'Monitor']),
            'model' => fake()->word(),
            'asset_number' => fake()->optional()->numerify('ASSET-#####'),
            'has_guarantee' => fake()->boolean(30),
        ];
    }

    /**
     * Device with guarantee
     */
    public function withGuarantee(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_guarantee' => true,
        ]);
    }

    /**
     * Device without guarantee
     */
    public function withoutGuarantee(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_guarantee' => false,
        ]);
    }

    /**
     * Device with specific type
     */
    public function laptop(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Laptop',
            'model' => fake()->randomElement(['ThinkPad', 'MacBook', 'Surface']),
        ]);
    }

    /**
     * Device with specific customer
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $customer->id,
        ]);
    }
}
