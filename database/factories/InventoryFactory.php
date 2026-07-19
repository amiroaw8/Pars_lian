<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'type' => fake()->randomElement(['device', 'part', 'tool', 'other']),
            'quantity' => fake()->numberBetween(0, 100),
            'price' => fake()->randomFloat(2, 10, 1000),
            'color' => fake()->safeColorName(),
            'description' => fake()->sentence(),
        ];
    }
}
