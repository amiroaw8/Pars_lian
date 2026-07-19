<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_product_count_includes_descendant_products_without_eager_loading(): void
    {
        $root = ProductCategory::factory()->create(['name' => 'پرینتر']);
        $child = ProductCategory::factory()->childOf($root)->create(['name' => 'HP']);
        $leaf = ProductCategory::factory()->childOf($child)->create(['name' => '2035']);

        Product::factory()->create(['category_id' => $leaf->id]);

        $freshRoot = ProductCategory::query()->findOrFail($root->id);

        $this->assertSame(1, $freshRoot->treeProductsCount());
        $this->assertSame(1, $freshRoot->productsInTreeCount());
    }

    public function test_tree_product_count_map_aggregates_entire_tree(): void
    {
        $root = ProductCategory::factory()->create(['name' => 'Root']);
        $child = ProductCategory::factory()->childOf($root)->create(['name' => 'Child']);
        $leaf = ProductCategory::factory()->childOf($child)->create(['name' => 'Leaf']);

        Product::factory()->create(['category_id' => $leaf->id]);
        Product::factory()->create(['category_id' => $child->id]);

        $map = ProductCategory::treeProductCountMap();

        $this->assertSame(2, $map[$root->id]);
        $this->assertSame(2, $map[$child->id]);
        $this->assertSame(1, $map[$leaf->id]);
    }

    public function test_options_for_select_lists_all_branch_levels_with_indent(): void
    {
        $root = ProductCategory::factory()->create(['name' => 'پرینتر', 'sort_order' => 1]);
        $child = ProductCategory::factory()->childOf($root)->create(['name' => 'HP', 'sort_order' => 1]);
        ProductCategory::factory()->childOf($child)->create(['name' => '2035', 'sort_order' => 1]);

        $options = ProductCategory::optionsForSelect();

        $this->assertSame('پرینتر', $options[$root->id]);
        $this->assertSame('— HP', $options[$child->id]);
        $this->assertCount(3, $options);
    }

    public function test_option_paths_for_select_builds_full_paths(): void
    {
        $root = ProductCategory::factory()->create(['name' => 'پرینتر']);
        $child = ProductCategory::factory()->childOf($root)->create(['name' => 'HP']);
        $leaf = ProductCategory::factory()->childOf($child)->create(['name' => '2035']);

        $paths = ProductCategory::optionPathsForSelect();

        $this->assertSame('پرینتر', $paths[$root->id]);
        $this->assertSame('پرینتر / HP', $paths[$child->id]);
        $this->assertSame('پرینتر / HP / 2035', $paths[$leaf->id]);
    }

    public function test_get_descendant_ids_including_self_returns_all_levels(): void
    {
        $root = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->childOf($root)->create();
        $leaf = ProductCategory::factory()->childOf($child)->create();

        $ids = $leaf->getDescendantIdsIncludingSelf();

        $this->assertSame([$leaf->id], $ids);

        $rootIds = $root->getDescendantIdsIncludingSelf();
        sort($rootIds);

        $expected = [$root->id, $child->id, $leaf->id];
        sort($expected);

        $this->assertSame($expected, $rootIds);
    }

    public function test_can_have_children_respects_max_depth(): void
    {
        $root = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->childOf($root)->create();
        $leaf = ProductCategory::factory()->childOf($child)->create();

        $this->assertTrue($root->canHaveChildren());
        $this->assertTrue($child->canHaveChildren());
        $this->assertFalse($leaf->canHaveChildren());
    }
}
