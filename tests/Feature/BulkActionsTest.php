<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\ProductsIndex;
use Tests\TestCase;

class BulkActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_bulk_actions_bar_appears_when_products_selected(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', [$products->first()->id])
            ->assertSee('1 product(s) selected')
            ->assertSee('Move to Trash')
            ->assertSee('Clear Selection');
    }

    public function test_bulk_actions_bar_shows_correct_count(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(5)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', [$products->get(0)->id, $products->get(1)->id, $products->get(2)->id])
            ->assertSee('3 product(s) selected');
    }

    public function test_select_all_checkbox_selects_all_current_page_products(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(5)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 3)
            ->set('selectAll', true)
            ->assertSet('selectedProducts', $products->take(3)->pluck('id')->map(fn($id) => (string) $id)->toArray());
    }

    public function test_select_all_checkbox_deselects_all_products(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(5)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 3)
            ->set('selectAll', true)
            ->set('selectAll', false)
            ->assertSet('selectedProducts', []);
    }

    public function test_select_all_checkbox_updates_when_individual_products_selected(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 3)
            ->set('selectedProducts', [$products->get(0)->id, $products->get(1)->id])
            ->assertSet('selectAll', false)
            ->set('selectedProducts', [$products->get(0)->id, $products->get(1)->id, $products->get(2)->id])
            ->assertSet('selectAll', true);
    }

    public function test_select_all_checkbox_updates_when_individual_products_deselected(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 3)
            ->set('selectAll', true)
            ->set('selectedProducts', [$products->get(0)->id, $products->get(1)->id])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_delete_moves_products_to_trash(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $productIds = $products->pluck('id')->toArray();

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $productIds)
            ->call('bulkDelete')
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);

        // Check that products are soft deleted
        foreach ($productIds as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }

    public function test_bulk_delete_shows_success_message(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(2)->create(['category_id' => $category->id]);

        $productIds = $products->pluck('id')->toArray();

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $productIds)
            ->call('bulkDelete');

        $this->assertTrue(session()->has('success'));
        $this->assertStringContainsString('2 product(s) moved to trash', session('success'));
    }

    public function test_bulk_delete_handles_empty_selection(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', [])
            ->call('bulkDelete')
            ->assertSet('selectedProducts', []);
    }

    public function test_bulk_delete_requires_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(2)->create(['category_id' => $category->id]);

        $productIds = $products->pluck('id')->toArray();

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $productIds)
            ->call('bulkDelete');

        // Products should not be deleted
        foreach ($productIds as $id) {
            $this->assertDatabaseHas('products', ['id' => $id]);
        }
    }

    public function test_bulk_delete_handles_partial_permissions(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $productIds = $products->pluck('id')->toArray();

        // Mock the authorize method to return false for one product
        $this->partialMock(ProductPolicy::class, function ($mock) use ($products, $user) {
            $mock->shouldReceive('delete')
                ->with($user, $products->get(0))
                ->andReturn(false);
            $mock->shouldReceive('delete')
                ->with($user, $products->get(1))
                ->andReturn(true);
            $mock->shouldReceive('delete')
                ->with($user, $products->get(2))
                ->andReturn(true);
        });

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $productIds)
            ->call('bulkDelete');

        // Only 2 products should be deleted
        $this->assertSoftDeleted('products', ['id' => $products->get(1)->id]);
        $this->assertSoftDeleted('products', ['id' => $products->get(2)->id]);
        $this->assertDatabaseHas('products', ['id' => $products->get(0)->id]);
    }

    public function test_clear_selection_resets_selection_state(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $products->pluck('id')->toArray())
            ->set('selectAll', true)
            ->call('clearSelection')
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_actions_work_with_pagination(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(25)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 10)
            ->set('selectAll', true)
            ->assertSet('selectedProducts', $products->take(10)->pluck('id')->map(fn($id) => (string) $id)->toArray());
    }

    public function test_bulk_actions_work_with_search_filters(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        
        $product1 = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'category_id' => $category->id
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'category_id' => $category->id
        ]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'iPhone')
            ->set('selectAll', true)
            ->assertSet('selectedProducts', [(string) $product1->id])
            ->assertDontSee($product2->name);
    }

    public function test_bulk_actions_work_with_category_filters(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Books']);
        
        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('categoryId', $category1->id)
            ->set('selectAll', true)
            ->assertSet('selectedProducts', [(string) $product1->id])
            ->assertDontSee($product2->name);
    }

    public function test_bulk_actions_work_with_sorting(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        
        $product1 = Product::factory()->create([
            'name' => 'Apple',
            'price' => 100.00,
            'category_id' => $category->id
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Banana',
            'price' => 50.00,
            'category_id' => $category->id
        ]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->call('sortBy', 'price')
            ->set('selectAll', true)
            ->assertSet('selectedProducts', [(string) $product2->id, (string) $product1->id]);
    }

    public function test_bulk_actions_clear_when_filters_change(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $products->pluck('id')->toArray())
            ->set('selectAll', true)
            ->set('search', 'new search')
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_actions_clear_when_per_page_changes(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(25)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 10)
            ->set('selectAll', true)
            ->set('perPage', 25)
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_actions_clear_when_sorting_changes(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $products->pluck('id')->toArray())
            ->set('selectAll', true)
            ->call('sortBy', 'price')
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_actions_work_with_empty_results(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'nonexistent product')
            ->set('selectAll', true)
            ->assertSet('selectedProducts', [])
            ->assertSet('selectAll', false);
    }

    public function test_bulk_actions_handle_mixed_permissions_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $productIds = $products->pluck('id')->toArray();

        // Mock the authorize method to return false for all products
        $this->partialMock(ProductPolicy::class, function ($mock) use ($products, $user) {
            $mock->shouldReceive('delete')
                ->andReturn(false);
        });

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('selectedProducts', $productIds)
            ->call('bulkDelete');

        // No products should be deleted
        foreach ($productIds as $id) {
            $this->assertDatabaseHas('products', ['id' => $id]);
        }

        // Should show error message
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('No products were deleted', session('error'));
    }
}
