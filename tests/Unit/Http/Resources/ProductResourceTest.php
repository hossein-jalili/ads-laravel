<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_default_price_when_no_price_rules_match()
    {
        $product = Product::factory()->create([
            'xentral_id' => 1,
            'sales_price_net' => 99.99,
        ]);

        $resource = new ProductResource($product);
        $data = $resource->toArray(request());

        $this->assertEquals([
            ['price' => 99.99, 'amount' => 1]
        ], $data['prices']);
    }

    #[Test]
    public function it_returns_customer_specific_price()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create(['xentral_id' => 10]);

        ProductPrice::factory()->create([
            'product_id' => 10,
            'customer_id' => $user->id,
            'amount' => 1,
            'price' => 20.50,
            'valid_from' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $resource = new ProductResource($product);
        $data = $resource->toArray(request());

        $this->assertCount(1, $data['prices']);
        $this->assertEquals(20.50, $data['prices'][0]->price);
        $this->assertEquals(1, $data['prices'][0]->amount);
    }

    #[Test]
    public function it_returns_group_price_if_customer_group_matches()
    {
        $user = User::factory()->make([
            'id' => 10,
            'groups' => [
                ['id' => 100],
                ['id' => 200],
            ],
        ]);
        Auth::login($user);
        $groupId = $user->groups[0]['id'];

        $product = Product::factory()->create(['xentral_id' => 11]);

        ProductPrice::factory()->create([
            'product_id' => 11,
            'customer_group_id' => $groupId,
            'amount' => 1,
            'price' => 30.25,
            'valid_from' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $resource = new ProductResource($product);
        $data = $resource->toArray(request());

        $this->assertEquals(30.25, $data['prices'][0]->price);
    }

    #[Test]
    public function it_filters_out_expired_prices()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create(['xentral_id' => 12]);

        ProductPrice::factory()->create([
            'product_id' => 12,
            'customer_id' => $user->id,
            'amount' => 1,
            'price' => 40.00,
            'valid_from' => now()->subDays(10),
            'expires_at' => now()->subDay(), // expired
        ]);

        $resource = new ProductResource($product);
        $data = $resource->toArray(request());

        // Should fall back to default product price
        $this->assertEquals($product->sales_price_net, $data['prices'][0]['price']);
    }

    #[Test]
    public function it_returns_lowest_price_per_amount()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create([
            'xentral_id' => 13,
            'sales_price_net' => 80.00
        ]);

        ProductPrice::factory()->create([
            'product_id' => 13,
            'customer_id' => $user->id,
            'amount' => 5,
            'price' => 100.00
        ]);

        ProductPrice::factory()->create([
            'product_id' => 13,
            'customer_id' => $user->id,
            'amount' => 5,
            'price' => 95.00
        ]);

        $resource = new ProductResource($product);
        $data = $resource->toArray(request());

        // Expect 2 entries: one for fallback (amount = 1), one for amount = 5
        $this->assertCount(2, $data['prices']);

        $amount5 = collect($data['prices'])->firstWhere('amount', 5);
        $this->assertEquals(95.00, $amount5->price);
    }
}
