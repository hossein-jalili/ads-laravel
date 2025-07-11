<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\ShippingCost;
use App\Services\ShippingCostService;
use App\Repositories\ShippingCostRepository;
use Mockery;

class ShippingCostServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function calculate_with_specific_country_and_paid_shipping()
    {
        $record = new ShippingCost([
            'country_code' => 'CA',
            'shipping_method' => 'FedEx',
            'free_shipping_limit' => 100.0,
            'shipping_cost' => 20.0,
            'customs_cost' => 8.0,
        ]);

        $mockRepo = Mockery::mock(ShippingCostRepository::class);
        $mockRepo->shouldReceive('getByCountryCode')->with('CA')->andReturn($record);

        $service = new ShippingCostService($mockRepo);
        $result = $service->calculate('CA', 50);

        $this->assertEquals([
            'shipping_method' => 'FedEx',
            'shipping_cost' => 20.0,
            'customs_cost' => 8.0,
        ], $result);
    }

    #[Test]
    public function calculate_with_default_shipping_cost()
    {
        $record = new ShippingCost([
            'country_code' => null,
            'shipping_method' => 'FedEx',
            'free_shipping_limit' => 100.0,
            'shipping_cost' => 20.0,
            'customs_cost' => 8.0,
        ]);

        $mockRepo = Mockery::mock(ShippingCostRepository::class);
        $mockRepo->shouldReceive('getByCountryCode')->with('CA')->andReturn(null);
        $mockRepo->shouldReceive('getDefault')->andReturn($record);

        $service = new ShippingCostService($mockRepo);
        $result = $service->calculate('CA', 50);

        $this->assertEquals([
            'shipping_method' => 'FedEx',
            'shipping_cost' => 20.0,
            'customs_cost' => 8.0,
        ], $result);
    }

    #[Test]
    public function calculate_when_no_shipping_data_exists()
    {
        $mockRepo = Mockery::mock(ShippingCostRepository::class);
        $mockRepo->shouldReceive('getByCountryCode')->with('ZZ')->andReturn(null);
        $mockRepo->shouldReceive('getDefault')->andReturn(null);

        $service = new ShippingCostService($mockRepo);
        $result = $service->calculate('ZZ', 10);

        $this->assertEquals([
            'shipping_method' => 'N/A',
            'shipping_cost' => 0.0,
            'customs_cost' => 0.0,
        ], $result);
    }

    #[Test]
    public function calculate_with_free_shipping()
    {
        $record = new ShippingCost([
            'country_code' => 'US',
            'shipping_method' => 'UPS',
            'free_shipping_limit' => 50.0,
            'shipping_cost' => 10.0,
            'customs_cost' => 5.0,
        ]);

        $mockRepo = Mockery::mock(ShippingCostRepository::class);
        $mockRepo->shouldReceive('getByCountryCode')->with('US')->andReturn($record);

        $service = new ShippingCostService($mockRepo);
        $result = $service->calculate('US', 60);

        $this->assertEquals([
            'shipping_method' => 'UPS',
            'shipping_cost' => 0.0,
            'customs_cost' => 0.0,
        ], $result);
    }
}
