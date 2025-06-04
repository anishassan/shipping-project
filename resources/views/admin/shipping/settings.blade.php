@extends('layouts.admin')

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
                    <form id="generalSettingsForm" action="{{ route('admin.shipping.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="general">
                        
                        <div class="mb-3">
                            <label class="form-label">Default Carrier</label>
                            <select class="form-select" name="default_carrier" required>
                                <option value="">Select</option>
                                @foreach($carriers as $carrier)
                                    <option value="{{ $carrier->id }}" {{ $settings->default_carrier == $carrier->id ? 'selected' : '' }}>
                                        {{ $carrier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Package Size</label>
                            <select class="form-select" name="default_package_size" required>
                                <option value="">Select</option>
                                <option value="small" {{ $settings->default_package_size == 'small' ? 'selected' : '' }}>Small</option>
                                <option value="medium" {{ $settings->default_package_size == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="large" {{ $settings->default_package_size == 'large' ? 'selected' : '' }}>Large</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Weight Unit</label>
                            <select class="form-select" name="default_weight_unit" required>
                                <option value="kg" {{ $settings->default_weight_unit == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="g" {{ $settings->default_weight_unit == 'g' ? 'selected' : '' }}>Gram (g)</option>
                                <option value="lb" {{ $settings->default_weight_unit == 'lb' ? 'selected' : '' }}>Pound (lb)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Currency</label>
                            <select class="form-select" name="default_currency" required>
                                <option value="USD" {{ $settings->default_currency == 'USD' ? 'selected' : '' }}>US Dollar ($)</option>
                                <option value="EUR" {{ $settings->default_currency == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                                <option value="GBP" {{ $settings->default_currency == 'GBP' ? 'selected' : '' }}>British Pound (£)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
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
                    <form id="notificationSettingsForm" action="{{ route('admin.shipping.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="notifications">
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_shipment_created" value="1" 
                                       {{ $settings->notify_on_shipment_created ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when new shipment is created</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_status_change" value="1" 
                                       {{ $settings->notify_on_status_change ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when shipment status changes</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_delivery" value="1" 
                                       {{ $settings->notify_on_delivery ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when shipment is delivered</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_on_failure" value="1" 
                                       {{ $settings->notify_on_failure ? 'checked' : '' }}>
                                <label class="form-check-label">Notify when delivery fails</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notification Methods</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_via_email" value="1" 
                                       {{ $settings->notify_via_email ? 'checked' : '' }}>
                                <label class="form-check-label">Email</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="notify_via_sms" value="1" 
                                       {{ $settings->notify_via_sms ? 'checked' : '' }}>
                                <label class="form-check-label">SMS</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Automation Settings</h5>
                </div>
                <div class="card-body">
                    <form id="automationSettingsForm" action="{{ route('admin.shipping.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="automation">
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="auto_create_label" value="1" 
                                       {{ $settings->auto_create_label ? 'checked' : '' }}>
                                <label class="form-check-label">Automatically create label when shipment is created</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="auto_update_status" value="1" 
                                       {{ $settings->auto_update_status ? 'checked' : '' }}>
                                <label class="form-check-label">Automatically update shipment status</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Update Interval (minutes)</label>
                            <input type="number" class="form-control" name="status_update_interval" 
                                   value="{{ $settings->status_update_interval }}" min="1" max="60">
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Form submission
    $('form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        
        submitButton.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                toastr.success('Settings updated successfully');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection 