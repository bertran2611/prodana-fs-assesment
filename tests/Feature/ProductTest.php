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

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_cannot_access_products(): void
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_products_index(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/products');
        $response->assertOk();
        $response->assertViewIs('products.index');
    }

    public function test_user_can_view_products_index_with_livewire(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->assertSee($products->first()->name)
            ->assertSee($category->name);
    }

    public function test_user_can_search_products(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        
        $product1 = Product::factory()->create([
            'name' => 'Test Product',
            'category_id' => $category->id
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Another Product',
            'category_id' => $category->id
        ]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'Test')
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
    }

    public function test_user_can_filter_products_by_category(): void
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

    public function test_user_can_sort_products(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        
        $product1 = Product::factory()->create(['name' => 'Apple', 'category_id' => $category->id]);
        $product2 = Product::factory()->create(['name' => 'Banana', 'category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->call('sortBy', 'name')
            ->assertSeeInOrder([$product1->name, $product2->name]);
    }

    public function test_user_can_change_per_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(25)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('perPage', 25)
            ->assertSee($products->first()->name);
    }

    public function test_user_can_clear_filters(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test(ProductsIndex::class)
            ->set('search', 'test')
            ->set('categoryId', $category->id)
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('categoryId', '');
    }

    public function test_user_can_view_product_details(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/products/{$product->id}");
        $response->assertOk();
        $response->assertViewIs('products.show');
        $response->assertSee($product->name);
    }

    public function test_user_can_view_product_create_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/products/create');
        $response->assertOk();
        $response->assertViewIs('products.create');
    }

    public function test_user_can_view_product_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/products/{$product->id}/edit");
        $response->assertOk();
        $response->assertViewIs('products.edit');
        $response->assertSee($product->name);
    }

    public function test_user_can_view_trash_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/products-trash');
        $response->assertOk();
        $response->assertViewIs('products.trash');
    }

    public function test_product_form_can_create_product(): void
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

    public function test_product_form_can_update_product(): void
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

    public function test_product_form_validates_required_fields(): void
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

    public function test_product_form_validates_price_format(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 'invalid-price')
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('save')
            ->assertHasErrors(['price']);
    }

    public function test_product_form_validates_stock_format(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->set('name', 'Test Product')
            ->set('price', 99.99)
            ->set('stock', 'invalid-stock')
            ->set('category_id', $category->id)
            ->call('save')
            ->assertHasErrors(['stock']);
    }

    public function test_product_form_validates_unique_name(): void
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

    public function test_product_form_can_handle_image_upload(): void
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

    public function test_product_form_validates_image_size(): void
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

    public function test_product_form_validates_image_type(): void
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

    public function test_live_validation_works_on_product_form(): void
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

    public function test_product_form_shows_categories_dropdown(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $categories = Category::factory()->count(3)->create();

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->assertSee($categories->first()->name)
            ->assertSee($categories->last()->name);
    }

    public function test_product_form_handles_edit_mode(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'category_id' => $category->id
        ]);

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['product' => $product])
            ->assertSet('name', 'Original Name')
            ->assertSet('mode', 'edit');
    }

    public function test_product_form_handles_create_mode(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($user)
            ->test(ProductForm::class)
            ->assertSet('mode', 'create');
    }
}
