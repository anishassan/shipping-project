<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'shippingCarrier', 'shippingService'])
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $carriers = ShippingCarrier::active()->get();
        return view('orders.create', compact('carriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_carrier_id' => 'required|exists:shipping_carriers,id',
            'shipping_service_id' => 'required|exists:shipping_services,id',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'shipping_carrier_id' => $request->shipping_carrier_id,
                'shipping_service_id' => $request->shipping_service_id,
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
            ]);

            // Add order items
            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'weight' => $item['weight'],
                ]);
            }

            // Calculate shipping cost
            $shippingCost = $order->calculateShippingCost();
            $order->update(['shipping_cost' => $shippingCost]);

            // Calculate total amount
            $totalAmount = $order->items->sum(function ($item) {
                return $item->price * $item->quantity;
            }) + $shippingCost;

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['user', 'shippingCarrier', 'shippingService', 'shippingAddress', 'billingAddress', 'items.product']);
        return view('orders.show', compact('order'));
    }

    public function process(Order $order)
    {
        try {
            DB::beginTransaction();

            // Generate shipping label
            if ($order->generateShippingLabel()) {
                $order->update(['status' => 'processing']);
                DB::commit();
                return back()->with('success', 'Order processed and shipping label generated successfully.');
            }

            DB::rollBack();
            return back()->with('error', 'Error generating shipping label.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing order: ' . $e->getMessage());
        }
    }

    public function getShippingServices(Request $request)
    {
        $carrier = ShippingCarrier::findOrFail($request->carrier_id);
        $services = $carrier->services()->active()->get();
        return response()->json($services);
    }

    public function calculateShippingCost(Request $request)
    {
        $request->validate([
            'carrier_id' => 'required|exists:shipping_carriers,id',
            'service_id' => 'required|exists:shipping_services,id',
            'from_country' => 'required|string|size:2',
            'to_country' => 'required|string|size:2',
            'weight' => 'required|numeric|min:0',
        ]);

        $rate = ShippingRate::where('carrier_id', $request->carrier_id)
            ->where('service_id', $request->service_id)
            ->where('from_country', $request->from_country)
            ->where('to_country', $request->to_country)
            ->first();

        if (!$rate) {
            return response()->json(['error' => 'No shipping rate found.'], 404);
        }

        $shippingCost = $rate->base_rate;
        if ($request->weight > $rate->min_weight) {
            $additionalWeight = $request->weight - $rate->min_weight;
            $shippingCost += ($additionalWeight * $rate->additional_rate);
        }

        return response()->json([
            'cost' => $shippingCost,
            'currency' => 'USD',
            'estimated_delivery' => $rate->estimated_delivery_days,
        ]);
    }
} 