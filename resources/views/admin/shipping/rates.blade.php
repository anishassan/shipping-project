@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Rates</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRateModal">
            <i class="fas fa-plus"></i> Add New Rate
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>From Country</th>
                            <th>To Country</th>
                            <th>Weight Range</th>
                            <th>Base Rate</th>
                            <th>Additional Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $rate)
                        <tr>
                            <td>{{ $rate->carrier->name }}</td>
                            <td>{{ $rate->service->name }}</td>
                            <td>{{ $rate->from_country }}</td>
                            <td>{{ $rate->to_country }}</td>
                            <td>
                                {{ $rate->min_weight }} - {{ $rate->max_weight }} {{ $rate->weight_unit }}
                            </td>
                            <td>${{ number_format($rate->base_rate, 2) }}</td>
                            <td>${{ number_format($rate->additional_rate, 2) }}/{{ $rate->weight_unit }}</td>
                            <td>
                                <span class="badge bg-{{ $rate->is_active ? 'success' : 'danger' }}">
                                    {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-warning edit-rate" 
                                            data-rate="{{ $rate->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editRateModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-rate" 
                                            data-rate="{{ $rate->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $rates->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Rate Modal -->
<div class="modal fade" id="addRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRateForm" action="{{ route('admin.shipping.rates.store') }}" method="POST">
                @csrf
                <div class="modal-body">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Country</label>
                                <select class="form-select" name="from_country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To Country</label>
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
                                <label class="form-label">Min Weight</label>
                                <input type="number" class="form-control" name="min_weight" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Weight</label>
                                <input type="number" class="form-control" name="max_weight" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Weight Unit</label>
                        <select class="form-select" name="weight_unit" required>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="lb">Pounds (lb)</option>
                            <option value="oz">Ounces (oz)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Base Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="base_rate" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Additional Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="additional_rate" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Country</label>
                                <select class="form-select" name="from_country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To Country</label>
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
                                <label class="form-label">Min Weight</label>
                                <input type="number" class="form-control" name="min_weight" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Weight</label>
                                <input type="number" class="form-control" name="max_weight" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Weight Unit</label>
                        <select class="form-select" name="weight_unit" required>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="lb">Pounds (lb)</option>
                            <option value="oz">Ounces (oz)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Base Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="base_rate" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Additional Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="additional_rate" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Rate</button>
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
        const serviceSelect = $(this).closest('form').find('select[name="service_id"]');
        
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

    // Edit Rate
    $('.edit-rate').click(function() {
        const rateId = $(this).data('rate');
        const form = $('#editRateForm');
        
        $.get(`/admin/shipping/rates/${rateId}/edit`, function(data) {
            form.attr('action', `/admin/shipping/rates/${rateId}`);
            form.find('[name="carrier_id"]').val(data.carrier_id).trigger('change');
            setTimeout(() => {
                form.find('[name="service_id"]').val(data.service_id);
            }, 500);
            form.find('[name="from_country"]').val(data.from_country);
            form.find('[name="to_country"]').val(data.to_country);
            form.find('[name="min_weight"]').val(data.min_weight);
            form.find('[name="max_weight"]').val(data.max_weight);
            form.find('[name="weight_unit"]').val(data.weight_unit);
            form.find('[name="base_rate"]').val(data.base_rate);
            form.find('[name="additional_rate"]').val(data.additional_rate);
            form.find('[name="is_active"]').prop('checked', data.is_active);
        });
    });

    // Delete Rate
    $('.delete-rate').click(function() {
        const rateId = $(this).data('rate');
        
        if (confirm('Are you sure you want to delete this shipping rate?')) {
            $.ajax({
                url: `/admin/shipping/rates/${rateId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection 