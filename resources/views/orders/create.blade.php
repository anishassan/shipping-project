@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Create New Order</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        <!-- Cart Items -->
                        <div class="mb-4">
                            <h5>Cart Items</h5>
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
                                    <tbody id="cartItems">
                                        <!-- Cart items will be dynamically added here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td id="subtotal">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                            <td id="shippingCost">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td id="total">$0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Shipping Address</h5>
                                <div class="mb-3">
                                    <select class="form-select" name="shipping_address_id" required>
                                        <option value="">Select Shipping Address</option>
                                        @foreach(auth()->user()->addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->name }} - {{ $address->address }}, {{ $address->city }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Billing Address</h5>
                                <div class="mb-3">
                                    <select class="form-select" name="billing_address_id" required>
                                        <option value="">Select Billing Address</option>
                                        @foreach(auth()->user()->addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->name }} - {{ $address->address }}, {{ $address->city }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Shipping Method</h5>
                                <div class="mb-3">
                                    <label class="form-label">Carrier</label>
                                    <select class="form-select" name="shipping_carrier_id" id="carrierSelect" required>
                                        <option value="">Select Carrier</option>
                                        @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Service</label>
                                    <select class="form-select" name="shipping_service_id" id="serviceSelect" required>
                                        <option value="">Select Service</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Cost</h5>
                                <div id="shippingDetails" class="p-3 bg-light rounded">
                                    <p class="mb-1">Select a carrier and service to calculate shipping cost.</p>
                                    <p class="mb-0" id="estimatedDelivery"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="mb-4">
                            <h5>Order Notes</h5>
                            <div class="mb-3">
                                <textarea class="form-control" name="notes" rows="3" placeholder="Add any special instructions or notes for this order..."></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load Services when Carrier is selected
    $('#carrierSelect').change(function() {
        const carrierId = $(this).val();
        const serviceSelect = $('#serviceSelect');
        
        if (carrierId) {
            $.get(`/orders/shipping-services?carrier_id=${carrierId}`, function(services) {
                serviceSelect.empty().append('<option value="">Select Service</option>');
                services.forEach(service => {
                    serviceSelect.append(`<option value="${service.id}">${service.name}</option>`);
                });
            });
        } else {
            serviceSelect.empty().append('<option value="">Select Service</option>');
        }
    });

    // Calculate Shipping Cost when Service is selected
    $('#serviceSelect').change(function() {
        const carrierId = $('#carrierSelect').val();
        const serviceId = $(this).val();
        const shippingAddress = $('select[name="shipping_address_id"]').val();
        const billingAddress = $('select[name="billing_address_id"]').val();
        const totalWeight = calculateTotalWeight();

        if (carrierId && serviceId && shippingAddress && billingAddress) {
            $.get('/orders/calculate-shipping', {
                carrier_id: carrierId,
                service_id: serviceId,
                from_country: $('#shippingAddress').data('country'),
                to_country: $('#billingAddress').data('country'),
                weight: totalWeight
            }, function(response) {
                $('#shippingCost').text(`$${response.cost.toFixed(2)}`);
                $('#total').text(`$${(parseFloat($('#subtotal').text().replace('$', '')) + response.cost).toFixed(2)}`);
                $('#estimatedDelivery').text(`Estimated delivery: ${response.estimated_delivery} days`);
            });
        }
    });

    // Calculate total weight of cart items
    function calculateTotalWeight() {
        let totalWeight = 0;
        $('#cartItems tr').each(function() {
            const weight = parseFloat($(this).find('.item-weight').text());
            const quantity = parseInt($(this).find('.item-quantity').val());
            totalWeight += weight * quantity;
        });
        return totalWeight;
    }

    // Form submission
    $('#orderForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                window.location.href = response.redirect;
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(field => {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>
@endpush
@endsection 