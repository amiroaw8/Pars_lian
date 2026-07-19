<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_all_active_products_without_filters(): void
    {
        Product::factory()->count(2)->create(['stock_quantity' => 5, 'stock_status' => 'instock']);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertViewHas('products', fn ($paginator) => $paginator->total() === 2);
    }

    public function test_category_and_brand_filters_combine_with_and_logic(): void
    {
        $laptopCategory = ProductCategory::factory()->create(['slug' => 'laptops', 'name' => 'لپ‌تاپ']);
        $phoneCategory = ProductCategory::factory()->create(['slug' => 'phones', 'name' => 'موبایل']);
        $hp = Brand::create(['slug' => 'hp', 'name' => 'HP', 'is_active' => true]);
        $samsung = Brand::create(['slug' => 'samsung', 'name' => 'Samsung', 'is_active' => true]);

        Product::factory()->create([
            'name' => 'HP Laptop',
            'slug' => 'hp-laptop',
            'category_id' => $laptopCategory->id,
            'brand_id' => $hp->id,
            'stock_quantity' => 3,
            'stock_status' => 'instock',
        ]);
        Product::factory()->create([
            'name' => 'Samsung Phone',
            'slug' => 'samsung-phone',
            'category_id' => $phoneCategory->id,
            'brand_id' => $samsung->id,
            'stock_quantity' => 3,
            'stock_status' => 'instock',
        ]);

        $response = $this->get(route('catalog.index', [
            'category' => ['laptops'],
            'brand' => [$hp->id],
        ]));

        $response->assertOk();
        $response->assertViewHas('products', fn ($paginator) => $paginator->total() === 1);
        $response->assertSee('HP Laptop', false);
        $response->assertDontSee('Samsung Phone', false);
    }

    public function test_nested_category_filter_selects_each_level_independently(): void
    {
        $printer = ProductCategory::factory()->create(['slug' => 'printer', 'name' => 'پرینتر']);
        $hp = ProductCategory::factory()->childOf($printer)->create(['slug' => 'hp-series', 'name' => 'HP']);
        $model2035 = ProductCategory::factory()->childOf($hp)->create(['slug' => 'hp-2035', 'name' => '2035']);
        $model2055 = ProductCategory::factory()->childOf($hp)->create(['slug' => 'hp-2055', 'name' => '2055']);

        Product::factory()->create([
            'name' => 'HP 2035 Printer',
            'slug' => 'hp-2035-printer',
            'category_id' => $model2035->id,
            'stock_quantity' => 2,
            'stock_status' => 'instock',
        ]);

        $leafOnly = $this->get(route('catalog.index', ['category' => [$model2035->id]]));
        $leafOnly->assertOk();
        $leafOnly->assertViewHas('products', fn ($paginator) => $paginator->total() === 1);
        $leafOnly->assertSee('HP 2035 Printer', false);

        $emptyLeaf = $this->get(route('catalog.index', ['category' => [$model2055->id]]));
        $emptyLeaf->assertOk();
        $emptyLeaf->assertViewHas('products', fn ($paginator) => $paginator->total() === 0);

        $parentBranch = $this->get(route('catalog.index', ['category' => [$hp->id]]));
        $parentBranch->assertOk();
        $parentBranch->assertViewHas('products', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_brands_are_synced_from_second_level_categories_when_missing(): void
    {
        $printer = ProductCategory::factory()->create(['slug' => 'printer', 'name' => 'پرینتر']);
        $hp = ProductCategory::factory()->childOf($printer)->create(['slug' => 'hp', 'name' => 'HP']);
        $model2035 = ProductCategory::factory()->childOf($hp)->create(['slug' => 'hp-2035', 'name' => '2035']);

        Product::factory()->create([
            'name' => 'HP 2035 Printer',
            'slug' => 'hp-2035-printer',
            'category_id' => $model2035->id,
            'stock_quantity' => 2,
            'stock_status' => 'instock',
        ]);

        $this->assertSame(0, Brand::count());

        $response = $this->get(route('catalog.index'));
        $response->assertOk();
        $response->assertSee('HP', false);

        $brand = Brand::where('slug', 'hp')->first();
        $this->assertNotNull($brand);
        $this->assertSame(1, Product::where('brand_id', $brand->id)->count());

        $filtered = $this->get(route('catalog.index', ['brand' => [$brand->id]]));
        $filtered->assertOk();
        $filtered->assertViewHas('products', fn ($paginator) => $paginator->total() === 1);
    }
}
