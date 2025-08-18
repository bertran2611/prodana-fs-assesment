<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected ProductPolicy $policy;
    protected User $adminUser;
    protected User $regularUser;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new ProductPolicy();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create(['category_id' => $this->category->id]);
        
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_view_any_products(): void
    {
        $this->assertTrue($this->policy->viewAny($this->adminUser));
    }

    public function test_regular_user_can_view_any_products(): void
    {
        $this->assertTrue($this->policy->viewAny($this->regularUser));
    }

    public function test_admin_can_view_product(): void
    {
        $this->assertTrue($this->policy->view($this->adminUser, $this->product));
    }

    public function test_regular_user_can_view_product(): void
    {
        $this->assertTrue($this->policy->view($this->regularUser, $this->product));
    }

    public function test_admin_can_create_product(): void
    {
        $this->assertTrue($this->policy->create($this->adminUser));
    }

    public function test_regular_user_cannot_create_product(): void
    {
        $this->assertFalse($this->policy->create($this->regularUser));
    }

    public function test_user_without_role_cannot_create_product(): void
    {
        $userWithoutRole = User::factory()->create(['role' => null]);
        $this->assertFalse($this->policy->create($userWithoutRole));
    }

    public function test_admin_can_update_product(): void
    {
        $this->assertTrue($this->policy->update($this->adminUser, $this->product));
    }

    public function test_regular_user_cannot_update_product(): void
    {
        $this->assertFalse($this->policy->update($this->regularUser, $this->product));
    }

    public function test_user_without_role_cannot_update_product(): void
    {
        $userWithoutRole = User::factory()->create(['role' => null]);
        $this->assertFalse($this->policy->update($userWithoutRole, $this->product));
    }

    public function test_admin_can_delete_product(): void
    {
        $this->assertTrue($this->policy->delete($this->adminUser, $this->product));
    }

    public function test_regular_user_cannot_delete_product(): void
    {
        $this->assertFalse($this->policy->delete($this->regularUser, $this->product));
    }

    public function test_user_without_role_cannot_delete_product(): void
    {
        $userWithoutRole = User::factory()->create(['role' => null]);
        $this->assertFalse($this->policy->delete($userWithoutRole, $this->product));
    }

    public function test_admin_can_restore_product(): void
    {
        $this->assertTrue($this->policy->restore($this->adminUser, $this->product));
    }

    public function test_regular_user_cannot_restore_product(): void
    {
        $this->assertFalse($this->policy->restore($this->regularUser, $this->product));
    }

    public function test_user_without_role_cannot_restore_product(): void
    {
        $userWithoutRole = User::factory()->create(['role' => null]);
        $this->assertFalse($this->policy->restore($userWithoutRole, $this->product));
    }

    public function test_admin_can_force_delete_product(): void
    {
        $this->assertTrue($this->policy->forceDelete($this->adminUser, $this->product));
    }

    public function test_regular_user_cannot_force_delete_product(): void
    {
        $this->assertFalse($this->policy->forceDelete($this->regularUser, $this->product));
    }

    public function test_user_without_role_cannot_force_delete_product(): void
    {
        $userWithoutRole = User::factory()->create(['role' => null]);
        $this->assertFalse($this->policy->forceDelete($userWithoutRole, $this->product));
    }

    public function test_policy_works_with_different_product_instances(): void
    {
        $product2 = Product::factory()->create(['category_id' => $this->category->id]);
        $product3 = Product::factory()->create(['category_id' => $this->category->id]);

        $this->assertTrue($this->policy->view($this->adminUser, $product2));
        $this->assertTrue($this->policy->view($this->adminUser, $product3));
        $this->assertTrue($this->policy->update($this->adminUser, $product2));
        $this->assertTrue($this->policy->update($this->adminUser, $product3));
        $this->assertTrue($this->policy->delete($this->adminUser, $product2));
        $this->assertTrue($this->policy->delete($this->adminUser, $product3));
    }

    public function test_policy_works_with_different_user_instances(): void
    {
        $admin2 = User::factory()->create(['role' => 'admin']);
        $admin3 = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($this->policy->create($admin2));
        $this->assertTrue($this->policy->create($admin3));
        $this->assertTrue($this->policy->update($admin2, $this->product));
        $this->assertTrue($this->policy->update($admin3, $this->product));
        $this->assertTrue($this->policy->delete($admin2, $this->product));
        $this->assertTrue($this->policy->delete($admin3, $this->product));
    }

    public function test_policy_works_with_soft_deleted_products(): void
    {
        $this->product->delete(); // Soft delete

        $this->assertTrue($this->policy->restore($this->adminUser, $this->product));
        $this->assertFalse($this->policy->restore($this->regularUser, $this->product));
    }

    public function test_policy_works_with_new_product_instances(): void
    {
        $newProduct = new Product();

        $this->assertTrue($this->policy->view($this->adminUser, $newProduct));
        $this->assertTrue($this->policy->view($this->regularUser, $newProduct));
        $this->assertTrue($this->policy->update($this->adminUser, $newProduct));
        $this->assertFalse($this->policy->update($this->regularUser, $newProduct));
        $this->assertTrue($this->policy->delete($this->adminUser, $newProduct));
        $this->assertFalse($this->policy->delete($this->regularUser, $newProduct));
    }

    public function test_policy_works_with_user_role_case_variations(): void
    {
        $adminUpperCase = User::factory()->create(['role' => 'ADMIN']);
        $adminMixedCase = User::factory()->create(['role' => 'Admin']);

        $this->assertTrue($this->policy->create($adminUpperCase));
        $this->assertTrue($this->policy->create($adminMixedCase));
        $this->assertTrue($this->policy->update($adminUpperCase, $this->product));
        $this->assertTrue($this->policy->update($adminMixedCase, $this->product));
        $this->assertTrue($this->policy->delete($adminUpperCase, $this->product));
        $this->assertTrue($this->policy->delete($adminMixedCase, $this->product));
    }

    public function test_policy_works_with_empty_string_role(): void
    {
        $userWithEmptyRole = User::factory()->create(['role' => '']);

        $this->assertFalse($this->policy->create($userWithEmptyRole));
        $this->assertFalse($this->policy->update($userWithEmptyRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithEmptyRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithEmptyRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithEmptyRole, $this->product));
    }

    public function test_policy_works_with_numeric_role(): void
    {
        $userWithNumericRole = User::factory()->create(['role' => 1]);

        $this->assertFalse($this->policy->create($userWithNumericRole));
        $this->assertFalse($this->policy->update($userWithNumericRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithNumericRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithNumericRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithNumericRole, $this->product));
    }

    public function test_policy_works_with_boolean_role(): void
    {
        $userWithBooleanRole = User::factory()->create(['role' => true]);

        $this->assertFalse($this->policy->create($userWithBooleanRole));
        $this->assertFalse($this->policy->update($userWithBooleanRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithBooleanRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithBooleanRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithBooleanRole, $this->product));
    }

    public function test_policy_works_with_array_role(): void
    {
        $userWithArrayRole = User::factory()->create(['role' => ['admin']]);

        $this->assertFalse($this->policy->create($userWithArrayRole));
        $this->assertFalse($this->policy->update($userWithArrayRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithArrayRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithArrayRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithArrayRole, $this->product));
    }

    public function test_policy_works_with_object_role(): void
    {
        $userWithObjectRole = User::factory()->create(['role' => (object)['type' => 'admin']]);

        $this->assertFalse($this->policy->create($userWithObjectRole));
        $this->assertFalse($this->policy->update($userWithObjectRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithObjectRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithObjectRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithObjectRole, $this->product));
    }

    public function test_policy_works_with_zero_role(): void
    {
        $userWithZeroRole = User::factory()->create(['role' => 0]);

        $this->assertFalse($this->policy->create($userWithZeroRole));
        $this->assertFalse($this->policy->update($userWithZeroRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithZeroRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithZeroRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithZeroRole, $this->product));
    }

    public function test_policy_works_with_negative_role(): void
    {
        $userWithNegativeRole = User::factory()->create(['role' => -1]);

        $this->assertFalse($this->policy->create($userWithNegativeRole));
        $this->assertFalse($this->policy->update($userWithNegativeRole, $this->product));
        $this->assertFalse($this->policy->delete($userWithNegativeRole, $this->product));
        $this->assertFalse($this->policy->restore($userWithNegativeRole, $this->product));
        $this->assertFalse($this->policy->forceDelete($userWithNegativeRole, $this->product));
    }
}
