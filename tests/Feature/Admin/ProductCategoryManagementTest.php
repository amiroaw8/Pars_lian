<?php

namespace Tests\Feature\Admin;

use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRoles;
use Tests\TestCase;

class ProductCategoryManagementTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    public function test_admin_can_view_category_index(): void
    {
        $admin = $this->createUserWithRole('admin');
        ProductCategory::factory()->create(['name' => 'پرینتر']);

        $response = $this->actingAsStaff($admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('پرینتر');
        $response->assertSee('مدیریت دسته‌بندی محصولات');
    }

    public function test_store_quick_creates_child_category_with_parent(): void
    {
        $admin = $this->createUserWithRole('admin');
        $parent = ProductCategory::factory()->create(['name' => 'پرینتر']);

        $response = $this->actingAsStaff($admin)->postJson(route('admin.categories.store-quick'), [
            'name' => 'HP',
            'parent_id' => $parent->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('category.name', 'HP')
            ->assertJsonPath('label', '— HP')
            ->assertJsonPath('path', 'پرینتر / HP')
            ->assertJsonPath('can_have_children', true);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'HP',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_quick_rejects_fourth_level_depth(): void
    {
        $admin = $this->createUserWithRole('admin');
        $root = ProductCategory::factory()->create(['name' => 'L1']);
        $child = ProductCategory::factory()->childOf($root)->create(['name' => 'L2']);
        $leaf = ProductCategory::factory()->childOf($child)->create(['name' => 'L3']);

        $response = $this->actingAsStaff($admin)->postJson(route('admin.categories.store-quick'), [
            'name' => 'L4',
            'parent_id' => $leaf->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('product_categories', ['name' => 'L4']);
    }
}
