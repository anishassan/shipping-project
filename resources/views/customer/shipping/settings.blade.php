@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Settings</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form id="generalSettingsForm" action="{{ route('customer.shipping.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Default Carrier</label>
                            <select class="form-select" name="default_carrier_id">
                                <option value="">Select Carrier</option>
                                @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ $settings->default_carrier_id == $carrier->id ? 'selected' : '' }}>
                                    {{ $carrier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Service</label>
                            <select class="form-select" name="default_service_id">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ $settings->default_service_id == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Package Size</label>
                            <select class="form-select" name="default_package_size">
                                <option value="small" {{ $settings->default_package_size == 'small' ? 'selected' : '' }}>Small</option>
                                <option value="medium" {{ $settings->default_package_size == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="large" {{ $settings->default_package_size == 'large' ? 'selected' : '' }}>Large</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Weight Unit</label>
                            <select class="form-select" name="default_weight_unit">
                                <option value="lb" {{ $settings->default_weight_unit == 'lb' ? 'selected' : '' }}>Pounds (lb)</option>
                                <option value="oz" {{ $settings->default_weight_unit == 'oz' ? 'selected' : '' }}>Ounces (oz)</option>
                                <option value="kg" {{ $settings->default_weight_unit == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notification Settings</h5>
                </div>
                <div class="card-body">
                    <form id="notificationSettingsForm" action="{{ route('customer.shipping.settings.notifications') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_shipment_created" value="1" 
                                       {{ $settings->notify_on_shipment_created ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when shipment is created</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_status_change" value="1" 
                                       {{ $settings->notify_on_status_change ? 'checked' : '' }}>
                                <label class="form-check-label">Notify on status changes</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_delivery" value="1" 
                                       {{ $settings->notify_on_delivery ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when package is delivered</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_exception" value="1" 
                                       {{ $settings->notify_on_exception ? 'checked' : '' }}>
                                <label class="form-check-label">Notify on delivery exceptions</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notification Email</label>
                            <input type="email" class="form-control" name="notification_email" 
                                   value="{{ $settings->notification_email }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // General Settings Form
    $('#generalSettingsForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                toastr.success('Settings updated successfully');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Notification Settings Form
    $('#notificationSettingsForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                toastr.success('Notification settings updated successfully');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });
});
</script>
@endpush
@endsection 