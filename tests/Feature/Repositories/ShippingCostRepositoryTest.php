<?php

namespace Tests\Feature\Repositories;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\ShippingCost;
use App\Repositories\ShippingCostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingCostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ShippingCostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ShippingCostRepository();
    }

    #[Test]
    public function it_returns_shipping_cost_by_country_code()
    {
        $shipping = ShippingCost::create([
            'country_code' => 'US',
            'shipping_method' => 'UPS',
            'free_shipping_limit' => 100.00,
            'shipping_cost' => 10.00,
            'customs_cost' => 5.00,
        ]);

        $result = $this->repository->getByCountryCode('us');

        $this->assertNotNull($result);
        $this->assertEquals($shipping->id, $result->id);
        $this->assertEquals('UPS', $result->shipping_method);
    }

    #[Test]
    public function it_returns_null_when_country_code_not_found()
    {
        $result = $this->repository->getByCountryCode('ZZ');

        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_default_shipping_cost_with_null_country_code()
    {
        $default = ShippingCost::create([
            'country_code' => null,
            'shipping_method' => 'DefaultCarrier',
            'free_shipping_limit' => 50.00,
            'shipping_cost' => 20.00,
            'customs_cost' => 10.00,
        ]);

        $result = $this->repository->getDefault();

        $this->assertNotNull($result);
        $this->assertEquals($default->id, $result->id);
        $this->assertNull($result->country_code);
    }

    #[Test]
    public function it_returns_null_if_no_default_shipping_cost()
    {
        $result = $this->repository->getDefault();
        $this->assertNull($result);
    }
}
