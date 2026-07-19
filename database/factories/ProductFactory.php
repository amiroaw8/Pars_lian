<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'category_id' => ProductCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'sku' => $this->faker->unique()->bothify('SKU-####-????'),
            'price' => $this->faker->randomFloat(2, 1000, 10000000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'manage_stock' => true,
            'stock_status' => 'instock',
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
