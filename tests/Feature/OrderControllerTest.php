<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use App\Models\ShippingRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $shippingAddress;
    protected $billingAddress;
    protected $carrier;
    protected $service;
    protected $product;

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

        // Create test product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'weight' => 2.5
        ]);
    }

    /** @test */
    public function it_can_create_new_order()
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_carrier_id' => $this->carrier->id,
            'shipping_service_id' => $this->service->id,
            'shipping_address_id' => $this->shippingAddress->id,
            'billing_address_id' => $this->billingAddress->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => $this->product->price,
                    'weight' => $this->product->weight
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'shipping_carrier_id' => $this->carrier->id,
            'shipping_service_id' => $this->service->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_order()
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), []);

        $response->assertSessionHasErrors([
            'shipping_carrier_id',
            'shipping_service_id',
            'shipping_address_id',
            'billing_address_id',
            'items'
        ]);
    }

    /** @test */
    public function it_can_process_order_and_generate_shipping_label()
    {
        // Create test order
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'shipping_carrier_id' => $this->carrier->id,
            'shipping_service_id' => $this->service->id,
            'shipping_address_id' => $this->shippingAddress->id,
            'billing_address_id' => $this->billingAddress->id,
            'status' => 'pending'
        ]);

        // Mock carrier's generateLabel method
        $this->carrier->shouldReceive('generateLabel')
            ->once()
            ->andReturn([
                'url' => 'https://example.com/label.pdf',
                'tracking_number' => 'TRACK123'
            ]);

        $response = $this->actingAs($this->user)
            ->post(route('orders.process', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
            'shipping_tracking_number' => 'TRACK123',
            'shipping_label_url' => 'https://example.com/label.pdf'
        ]);
    }

    /** @test */
    public function it_can_calculate_shipping_cost()
    {
        $response = $this->actingAs($this->user)->get(route('orders.calculate-shipping'), [
            'carrier_id' => $this->carrier->id,
            'service_id' => $this->service->id,
            'from_country' => 'US',
            'to_country' => 'CA',
            'weight' => 5.0
        ]);

        $response->assertOk();
        $response->assertJson([
            'cost' => 18.00, // Base rate (10.00) + Additional weight (4kg * 2.00)
            'currency' => 'USD',
            'estimated_delivery' => 3
        ]);
    }

    /** @test */
    public function it_returns_error_when_shipping_rate_not_found()
    {
        $response = $this->actingAs($this->user)->get(route('orders.calculate-shipping'), [
            'carrier_id' => $this->carrier->id,
            'service_id' => $this->service->id,
            'from_country' => 'US',
            'to_country' => 'UK', // Non-existent route
            'weight' => 5.0
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'No shipping rate found.'
        ]);
    }

    /** @test */
    public function it_can_get_shipping_services_for_carrier()
    {
        $response = $this->actingAs($this->user)->get(route('orders.shipping-services'), [
            'carrier_id' => $this->carrier->id
        ]);

        $response->assertOk();
        $response->assertJson([
            [
                'id' => $this->service->id,
                'name' => 'Express',
                'code' => 'EXP'
            ]
        ]);
    }
} 