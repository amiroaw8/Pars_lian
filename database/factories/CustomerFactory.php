<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '09'.fake()->unique()->numerify('#########'),
            'address' => fake()->address(),
        ];
    }

    /**
     * Customer without address
     */
    public function withoutAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => null,
        ]);
    }

    /**
     * Customer with specific name pattern
     */
    public function withCompanyName(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->company(),
        ]);
    }
}
