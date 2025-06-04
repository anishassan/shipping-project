@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Calculate Shipping Cost</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipping Details</h5>
                </div>
                <div class="card-body">
                    <form id="calculateForm">
                        <div class="mb-3">
                            <label class="form-label">From Address</label>
                            <select class="form-select" name="from_address_id" required>
                                <option value="">Select Address</option>
                                @foreach($addresses as $address)
                                <option value="{{ $address->id }}">{{ $address->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To Address</label>
                            <select class="form-select" name="to_address_id" required>
                                <option value="">Select Address</option>
                                @foreach($addresses as $address)
                                <option value="{{ $address->id }}">{{ $address->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Package Size</label>
                            <select class="form-select" name="package_size" required>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Weight</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="weight" step="0.01" required>
                                <select class="form-select" name="weight_unit" style="max-width: 100px;">
                                    <option value="lb">lb</option>
                                    <option value="oz">oz</option>
                                    <option value="kg">kg</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Carrier</label>
                            <select class="form-select" name="carrier_id" required>
                                <option value="">Select Carrier</option>
                                @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">Select Service</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calculator"></i> Calculate Cost
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Results</h5>
                </div>
                <div class="card-body">
                    <div id="results" style="display: none;">
                        <div class="alert alert-info">
                            <h5 class="alert-heading">Estimated Shipping Cost</h5>
                            <p class="mb-0" id="shippingCost"></p>
                        </div>

                        <div class="mb-3">
                            <h6>Delivery Time</h6>
                            <p class="mb-0" id="deliveryTime"></p>
                        </div>

                        <div class="mb-3">
                            <h6>Service Details</h6>
                            <p class="mb-0" id="serviceDetails"></p>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-success" id="createShipment">
                                <i class="fas fa-shipping-fast"></i> Create Shipment
                            </button>
                        </div>
                    </div>

                    <div id="noResults" class="text-center py-5">
                        <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                        <h5>No Results Yet</h5>
                        <p class="text-muted">Fill in the shipping details and click calculate to see the results.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load Services when Carrier is selected
    $('select[name="carrier_id"]').change(function() {
        const carrierId = $(this).val();
        const serviceSelect = $('select[name="service_id"]');
        
        serviceSelect.html('<option value="">Select Service</option>');
        
        if (carrierId) {
            $.get(`/customer/shipping/carriers/${carrierId}/services`, function(services) {
                services.forEach(function(service) {
                    serviceSelect.append(`
                        <option value="${service.id}">${service.name}</option>
                    `);
                });
            });
        }
    });

    // Calculate Form Submit
    $('#calculateForm').submit(function(e) {
        e.preventDefault();
        
        $.post('{{ route("customer.shipping.calculate") }}', $(this).serialize(), function(response) {
            $('#shippingCost').text(`$${response.cost.toFixed(2)}`);
            $('#deliveryTime').text(response.delivery_time);
            $('#serviceDetails').text(response.service_details);
            
            $('#noResults').hide();
            $('#results').show();
            
            // Store shipment data for creation
            $('#createShipment').data('shipment', response.shipment_data);
        }).fail(function() {
            toastr.error('An error occurred while calculating shipping cost. Please try again.');
        });
    });

    // Create Shipment
    $('#createShipment').click(function() {
        const shipmentData = $(this).data('shipment');
        
        $.post('{{ route("customer.shipping.store") }}', {
            _token: '{{ csrf_token() }}',
            ...shipmentData
        }, function(response) {
            window.location.href = response.redirect_url;
        }).fail(function() {
            toastr.error('An error occurred while creating the shipment. Please try again.');
        });
    });
});
</script>
@endpush
@endsection 