@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Shipment</h1>
        <a href="{{ route('admin.shipping.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.shipping.store') }}" method="POST" id="createShipmentForm">
                @csrf
                <div class="row">
                    <!-- From Address -->
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">From Address</h5>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="from_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" name="from_company">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" name="from_address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" name="from_address2">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="from_city" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="from_state" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" name="from_postal_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <select class="form-select" name="from_country" required>
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="MX">Mexico</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="from_phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="from_email" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- To Address -->
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">To Address</h5>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="to_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" name="to_company">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" name="to_address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" name="to_address2">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="to_city" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="to_state" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" name="to_postal_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <select class="form-select" name="to_country" required>
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="MX">Mexico</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="to_phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="to_email" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Package Details -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="card-title mb-3">Package Details</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Weight</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="weight" step="0.01" required>
                                        <select class="form-select" name="weight_unit" style="max-width: 100px;">
                                            <option value="kg">kg</option>
                                            <option value="lb">lb</option>
                                            <option value="oz">oz</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Length</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="length" step="0.01" required>
                                        <select class="form-select" name="dimension_unit" style="max-width: 100px;">
                                            <option value="cm">cm</option>
                                            <option value="in">in</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Width</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="width" step="0.01" required>
                                        <select class="form-select" name="dimension_unit" style="max-width: 100px;">
                                            <option value="cm">cm</option>
                                            <option value="in">in</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Height</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="height" step="0.01" required>
                                        <select class="form-select" name="dimension_unit" style="max-width: 100px;">
                                            <option value="cm">cm</option>
                                            <option value="in">in</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Package Type</label>
                                    <select class="form-select" name="package_type" required>
                                        <option value="">Select Package Type</option>
                                        <option value="package">Package</option>
                                        <option value="envelope">Envelope</option>
                                        <option value="box">Box</option>
                                        <option value="pallet">Pallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="reference_number">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Shipping Details -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="card-title mb-3">Shipping Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Carrier</label>
                                    <select class="form-select" name="carrier_id" required>
                                        <option value="">Select Carrier</option>
                                        @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Service</label>
                                    <select class="form-select" name="service_id" required>
                                        <option value="">Select Service</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Shipping Date</label>
                                    <input type="date" class="form-control" name="shipping_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Insurance Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="insurance_value" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Shipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load Services
    $('select[name="carrier_id"]').change(function() {
        const carrierId = $(this).val();
        const serviceSelect = $('select[name="service_id"]');
        
        if (carrierId) {
            $.get(`/admin/shipping/carriers/${carrierId}/services`, function(services) {
                serviceSelect.empty().append('<option value="">Select Service</option>');
                services.forEach(service => {
                    serviceSelect.append(`<option value="${service.id}">${service.name}</option>`);
                });
            });
        } else {
            serviceSelect.empty().append('<option value="">Select Service</option>');
        }
    });

    // Form Submission
    $('#createShipmentForm').submit(function(e) {
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

    // Remove validation on input
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
@endpush
@endsection 