@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Order #{{ $order->order_number }}</h4>
                    <div>
                        @if($order->status === 'pending')
                        <form action="{{ route('orders.process', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Process Order
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Order Status -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-{{ $order->status_color }}">
                                <h5 class="alert-heading">Order Status: {{ ucfirst($order->status) }}</h5>
                                @if($order->shipping_tracking_number)
                                <p class="mb-0">
                                    Tracking Number: {{ $order->shipping_tracking_number }}
                                    <a href="{{ route('shipping.track', $order->shipping_tracking_number) }}" 
                                       class="btn btn-sm btn-info ms-2">
                                        <i class="fas fa-search"></i> Track Shipment
                                    </a>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Order Number:</th>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Shipping Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Carrier:</th>
                                    <td>{{ $order->shippingCarrier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Service:</th>
                                    <td>{{ $order->shippingService->name }}</td>
                                </tr>
                                <tr>
                                    <th>Shipping Cost:</th>
                                    <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                                @if($order->shipping_tracking_number)
                                <tr>
                                    <th>Tracking Number:</th>
                                    <td>
                                        {{ $order->shipping_tracking_number }}
                                        @if($order->shipping_label_url)
                                        <a href="{{ $order->shipping_label_url }}" 
                                           class="btn btn-sm btn-success ms-2" 
                                           target="_blank">
                                            <i class="fas fa-download"></i> Download Label
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Shipping Address</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1">{{ $order->shippingAddress->name }}</p>
                                    <p class="mb-1">{{ $order->shippingAddress->company }}</p>
                                    <p class="mb-1">{{ $order->shippingAddress->address }}</p>
                                    <p class="mb-1">{{ $order->shippingAddress->address2 }}</p>
                                    <p class="mb-1">
                                        {{ $order->shippingAddress->city }}, 
                                        {{ $order->shippingAddress->state }} 
                                        {{ $order->shippingAddress->postal_code }}
                                    </p>
                                    <p class="mb-1">{{ $order->shippingAddress->country }}</p>
                                    <p class="mb-0">
                                        Phone: {{ $order->shippingAddress->phone }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Billing Address</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1">{{ $order->billingAddress->name }}</p>
                                    <p class="mb-1">{{ $order->billingAddress->company }}</p>
                                    <p class="mb-1">{{ $order->billingAddress->address }}</p>
                                    <p class="mb-1">{{ $order->billingAddress->address2 }}</p>
                                    <p class="mb-1">
                                        {{ $order->billingAddress->city }}, 
                                        {{ $order->billingAddress->state }} 
                                        {{ $order->billingAddress->postal_code }}
                                    </p>
                                    <p class="mb-1">{{ $order->billingAddress->country }}</p>
                                    <p class="mb-0">
                                        Phone: {{ $order->billingAddress->phone }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Order Items</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Weight</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->weight }} kg</td>
                                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td>${{ number_format($order->total_amount - $order->shipping_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                            <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Order Notes</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $order->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 