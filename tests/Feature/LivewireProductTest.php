<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Livewire\ProductsIndex;
use App\Livewire\ProductForm;
use Tests\TestCase;

class LivewireProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_products_index_component_loads_with_data(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(5)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->assertSee($products->first()->name)
            ->assertSee($products->last()->name)
            ->assertSee($category->name);
    }

    public function test_products_index_component_handles_empty_state(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->assertSee('No products found')
            ->assertSee('Get started by creating your first product');
    }

    public function test_products_index_component_handles_search(): void
    {
        $user = User::factory()->create(['role' => 'user']);
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
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
    }

    public function test_products_index_component_handles_category_filter(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Books']);
        
        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('categoryId', $category1->id)
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
    }

    public function test_products_index_component_handles_sorting(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        
        $product1 = Product::factory()->create([
            'name' => 'Apple',
            'price' => 100.00,
            'stock' => 5,
            'category_id' => $category->id
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Banana',
            'price' => 50.00,
            'stock' => 10,
            'category_id' => $category->id
        ]);

        // Test name sorting
        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->call('sortBy', 'name')
            ->assertSeeInOrder([$product1->name, $product2->name]);

        // Test price sorting
        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->call('sortBy', 'price')
            ->assertSeeInOrder([$product2->name, $product1->name]);

        // Test stock sorting
        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->call('sortBy', 'stock')
            ->assertSeeInOrder([$product1->name, $product2->name]);
    }

    public function test_products_index_component_handles_pagination(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(25)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 10)
            ->assertSee($products->first()->name)
            ->assertSee($products->get(9)->name)
            ->assertDontSee($products->get(10)->name);
    }

    public function test_products_index_component_handles_clear_filters(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'test search')
            ->set('categoryId', $category->id)
            ->set('sortField', 'price')
            ->set('sortDir', 'desc')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('categoryId', '')
            ->assertSet('sortField', 'name')
            ->assertSet('sortDir', 'asc');
    }

    public function test_products_index_component_shows_filtered_count(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(5)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'test')
            ->assertSee('Showing 0 of 5 products');
    }

    public function test_product_form_component_creates_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'description' => 'Test description'
        ];

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', $productData['name'])
            ->set('price', $productData['price'])
            ->set('stock', $productData['stock'])
            ->set('category_id', $productData['category_id'])
            ->set('description', $productData['description'])
            ->call('save')
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', $productData);
    }

    public function test_product_form_component_updates_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $updatedData = [
            'name' => 'Updated Product',
            'price' => 149.99,
            'stock' => 20,
            'category_id' => $category->id,
            'description' => 'Updated description'
        ];

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['product' => $product])
            ->set('name', $updatedData['name'])
            ->set('price', $updatedData['price'])
            ->set('stock', $updatedData['stock'])
            ->set('description', $updatedData['description'])
            ->call('save')
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', $updatedData);
    }

    public function test_product_form_component_validates_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', '')
            ->set('price', '')
            ->set('stock', '')
            ->set('category_id', '')
            ->call('save')
            ->assertHasErrors(['name', 'price', 'stock', 'category_id']);
    }

    public function test_product_form_component_validates_field_formats(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 'invalid-price')
            ->set('stock', 'invalid-stock')
            ->set('category_id', $category->id)
            ->call('save')
            ->assertHasErrors(['price', 'stock']);
    }

    public function test_product_form_component_validates_unique_name(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $existingProduct = Product::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', $existingProduct->name)
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_product_form_component_allows_same_name_in_edit_mode(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['product' => $product])
            ->set('name', $product->name) // Same name
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertRedirect('/products');
    }

    public function test_product_form_component_handles_image_upload(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $image = UploadedFile::fake()->image('product.jpg', 100, 100);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->set('image', $image)
            ->call('save')
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'image_path' => 'products/' . $image->hashName()
        ]);
    }

    public function test_product_form_component_validates_image_size(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        // Create a file larger than 1MB
        $largeImage = UploadedFile::fake()->create('large.jpg', 1025);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->set('image', $largeImage)
            ->call('save')
            ->assertHasErrors(['image']);
    }

    public function test_product_form_component_validates_image_type(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $invalidFile = UploadedFile::fake()->create('document.txt', 100);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->set('image', $invalidFile)
            ->call('save')
            ->assertHasErrors(['image']);
    }

    public function test_product_form_component_shows_live_validation(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', '')
            ->assertHasErrors(['name'])
            ->set('name', 'Valid Name')
            ->assertHasNoErrors(['name']);
    }

    public function test_product_form_component_shows_categories_dropdown(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $categories = Category::factory()->count(3)->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->assertSee($categories->first()->name)
            ->assertSee($categories->last()->name);
    }

    public function test_product_form_component_handles_edit_mode(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'description' => 'Original description'
        ]);

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['product' => $product])
            ->assertSet('name', 'Original Name')
            ->assertSet('price', 99.99)
            ->assertSet('stock', 10)
            ->assertSet('category_id', $category->id)
            ->assertSet('description', 'Original description')
            ->assertSet('mode', 'edit');
    }

    public function test_product_form_component_handles_create_mode(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->assertSet('mode', 'create')
            ->assertSet('name', '')
            ->assertSet('price', '')
            ->assertSet('stock', '')
            ->assertSet('category_id', '')
            ->assertSet('description', '');
    }

    public function test_product_form_component_requires_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertForbidden();
    }

    public function test_product_form_component_requires_admin_role_for_edit(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['product' => $product])
            ->set('name', 'Updated Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertForbidden();
    }

    public function test_product_form_component_handles_validation_errors_properly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', '')
            ->set('price', -100)
            ->set('stock', -10)
            ->set('category_id', 99999) // Non-existent category
            ->call('save')
            ->assertHasErrors(['name', 'price', 'stock', 'category_id']);
    }

    public function test_product_form_component_handles_success_messages(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertRedirect('/products');

        // Check if success message is set
        $this->assertTrue(session()->has('success'));
    }
}
