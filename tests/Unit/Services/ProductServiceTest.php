<?php

namespace Tests\Unit\Services;

use App\Models\FakeCustomer;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;
    private FakeCustomer $userWithGroupAndPrices;
    private FakeCustomer $basicUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();

        // --- Create Fake Users ---
        // This user has a specific ID and group for testing targeted price rules.
        $this->userWithGroupAndPrices = new FakeCustomer([
            'id' => 10,
            'name' => 'Special Customer',
            'groups' => [['id' => 100, 'name' => 'Test Group']]
        ]);

        // This user has no special groups or prices.
        $this->basicUser = new FakeCustomer(['id' => 20, 'name' => 'Basic Customer']);

        // --- Create Products ---
        Product::factory()->create(['id' => 1, 'xentral_id' => 1, 'name' => 'Public Product 1', 'sales_price_gross' => 100 ,'sales_price_net' => 100]);
        Product::factory()->create(['id' => 2, 'xentral_id' => 2, 'name' => 'User-Specific Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 3, 'xentral_id' => 3, 'name' => 'Group-Specific Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 4, 'xentral_id' => 4, 'name' => 'Publicly-Priced Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 5, 'xentral_id' => 5, 'name' => 'Expired Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 6, 'xentral_id' => 6, 'name' => 'Future Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 7, 'xentral_id' => 7, 'name' => 'Hidden Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 8, 'xentral_id' => 8, 'name' => 'Zero Price Product', 'sales_price_gross' => 0,'sales_price_net' => 0]);
        Product::factory()->create(['id' => 9, 'xentral_id' => 9, 'name' => 'Disabled Product', 'is_disabled' => true, 'sales_price_gross' => 100]);

        // --- Create Price Rules ---
        // For product 2: specific to user with ID 10
        ProductPrice::factory()->create(['product_id' => 2, 'customer_id' => $this->userWithGroupAndPrices->id, 'price' => 50]);
        // For product 3: specific to group with ID 100
        ProductPrice::factory()->create(['product_id' => 3, 'customer_group_id' => 100, 'price' => 60]);
        // For product 4: public price, visible to all authenticated users
        ProductPrice::factory()->create(['product_id' => 4, 'customer_id' => null, 'customer_group_id' => null, 'price' => 70]);
        // For product 5: expired price
        ProductPrice::factory()->create(['product_id' => 5, 'customer_id' => null, 'expires_at' => now()->subDay(), 'price' => 80]);
        // For product 6: future price
        ProductPrice::factory()->create(['product_id' => 6, 'customer_id' => null, 'valid_from' => now()->addDay(), 'price' => 90]);
        // For product 8: zero price
        ProductPrice::factory()->create(['product_id' => 8, 'customer_id' => null, 'price' => 0]);
    }

    #[Test]
    public function a_basic_user_sees_only_publicly_available_products()
    {
        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertCount(2, $products, 'A basic user should only see publicly available products');
        $this->assertTrue($products->contains('id', 1)); // Public Product 1 (via gross price)
        $this->assertTrue($products->contains('id', 4)); // Publicly-Priced Product (via public price rule)
        $this->assertFalse($products->contains('id', 2)); // User specific
        $this->assertFalse($products->contains('id', 3)); // Group specific
    }

    #[Test]
    public function an_authenticated_user_sees_their_specific_and_group_products_plus_public_ones()
    {
        $query = $this->productService->getVisibleProductsQuery($this->userWithGroupAndPrices);
        $products = $query->get();

        $this->assertCount(4, $products, 'Authenticated user should see 4 products');
        $this->assertTrue($products->contains('id', 1)); // Public via gross price
        $this->assertTrue($products->contains('id', 2)); // Their specific product
        $this->assertTrue($products->contains('id', 3)); // Their group's product
        $this->assertTrue($products->contains('id', 4)); // Public via price rule
    }

    #[Test]
    public function a_user_without_a_group_cannot_see_group_specific_products()
    {
        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertCount(2, $products); // Sees public ones
        $this->assertFalse($products->contains('id', 3), 'Should not see the group-specific product');
    }

    #[Test]
    public function products_with_expired_or_future_prices_are_not_visible_to_any_user()
    {
        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertFalse($products->contains('id', 5), 'Expired product should not be visible.');
        $this->assertFalse($products->contains('id', 6), 'Future product should not be visible.');
    }

    #[Test]
    public function a_product_with_no_price_information_is_not_visible_to_any_user()
    {
        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertFalse($products->contains('id', 7), 'Product with no price should not be visible.');
    }

    #[Test]
    public function a_product_with_a_zero_price_rule_is_not_visible_to_any_user()
    {
        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertFalse($products->contains('id', 8), 'Product with zero price rule should not be visible.');
    }

    #[Test]
    public function product_with_zero_price_rule_is_filtered_by_price_greater_than_zero()
    {
        // Create a product with zero sales_price_gross
        $product = Product::factory()->create([
            'id' => 100,
            'xentral_id' => 100,
            'name' => 'Zero Price Test Product',
            'sales_price_net' => 0,
        ]);

        // Attach a zero-price rule
        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'customer_id' => $this->basicUser->id,
            'price' => 0,
        ]);

        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertFalse($products->contains('id', $product->id), 'Product with a zero-price rule should not be visible.');

        // Update price to a positive value and confirm it's now visible
        $product->prices()->first()->update(['price' => 25]);

        $query = $this->productService->getVisibleProductsQuery($this->basicUser);
        $products = $query->get();

        $this->assertTrue($products->contains('id', $product->id), 'Product with a valid price rule (> 0) should now be visible.');
    }

    #[Test]
    public function a_disabled_product_is_not_visible_to_any_user()
    {
        // Basic user check
        $basicQuery = $this->productService->getVisibleProductsQuery($this->basicUser);
        $basicProducts = $basicQuery->get();

        $this->assertFalse($basicProducts->contains('id', 9), 'Disabled product should not be visible to a basic user.');

        // Authenticated user check
        $authQuery = $this->productService->getVisibleProductsQuery($this->userWithGroupAndPrices);
        $authProducts = $authQuery->get();

        $this->assertFalse($authProducts->contains('id', 9), 'Disabled product should not be visible to an authenticated user.');
    }
}
