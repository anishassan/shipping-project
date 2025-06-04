<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use App\Models\ShippingRate;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $order;
    protected $user;
    protected $shippingAddress;
    protected $billingAddress;
    protected $carrier;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();

        // Create test addresses
        $this->shippingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country' => 'US'
        ]);

        $this->billingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country' => 'CA'
        ]);

        // Create test carrier and service
        $this->carrier = ShippingCarrier::factory()->create([
            'name' => 'Test Carrier',
            'code' => 'TEST',
            'status' => 'active'
        ]);

        $this->service = ShippingService::factory()->create([
            'carrier_id' => $this->carrier->id,
            'name' => 'Express',
            'code' => 'EXP',
            'status' => 'active'
        ]);

        // Create shipping rate
        ShippingRate::factory()->create([
            'carrier_id' => $this->carrier->id,
            'service_id' => $this->service->id,
            'from_country' => 'US',
            'to_country' => 'CA',
            'base_rate' => 10.00,
            'min_weight' => 1.0,
            'additional_rate' => 2.00
        ]);

        // Create test order
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'shipping_carrier_id' => $this->carrier->id,
            'shipping_service_id' => $this->service->id,
            'shipping_address_id' => $this->shippingAddress->id,
            'billing_address_id' => $this->billingAddress->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_can_calculate_shipping_cost()
    {
        // Create test product
        $product = Product::factory()->create([
            'weight' => 2.5
        ]);

        // Add item to order
        $this->order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 10.00,
            'weight' => $product->weight
        ]);

        // Calculate shipping cost
        $shippingCost = $this->order->calculateShippingCost();

        // Base rate (10.00) + Additional weight (4kg * 2.00)
        $expectedCost = 10.00 + (4 * 2.00);
        $this->assertEquals($expectedCost, $shippingCost);
    }

    /** @test */
    public function it_returns_zero_shipping_cost_when_carrier_or_service_not_set()
    {
        $order = Order::factory()->create([
            'shipping_carrier_id' => null,
            'shipping_service_id' => null
        ]);

        $this->assertEquals(0, $order->calculateShippingCost());
    }

    /** @test */
    public function it_can_generate_shipping_label()
    {
        // Mock carrier's generateLabel method
        $this->carrier->shouldReceive('generateLabel')
            ->once()
            ->andReturn([
                'url' => 'https://example.com/label.pdf',
                'tracking_number' => 'TRACK123'
            ]);

        $result = $this->order->generateShippingLabel();

        $this->assertTrue($result);
        $this->assertEquals('TRACK123', $this->order->shipping_tracking_number);
        $this->assertEquals('https://example.com/label.pdf', $this->order->shipping_label_url);
        $this->assertEquals('shipped', $this->order->status);
    }

    /** @test */
    public function it_handles_shipping_label_generation_failure()
    {
        // Mock carrier's generateLabel method to throw exception
        $this->carrier->shouldReceive('generateLabel')
            ->once()
            ->andThrow(new \Exception('Label generation failed'));

        $result = $this->order->generateShippingLabel();

        $this->assertFalse($result);
        $this->assertNull($this->order->shipping_tracking_number);
        $this->assertNull($this->order->shipping_label_url);
        $this->assertEquals('pending', $this->order->status);
    }
} 