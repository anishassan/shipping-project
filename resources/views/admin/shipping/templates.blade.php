@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Templates</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
            <i class="fas fa-plus"></i> Add New Template
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>Package Size</th>
                            <th>Default</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                        <tr>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->carrier->name }}</td>
                            <td>{{ $template->service->name }}</td>
                            <td>{{ $template->package_size }}</td>
                            <td>
                                <span class="badge bg-{{ $template->is_default ? 'success' : 'secondary' }}">
                                    {{ $template->is_default ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info edit-template" 
                                            data-template="{{ $template->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTemplateModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-template" 
                                            data-template="{{ $template->id }}">
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
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTemplateForm" action="{{ route('admin.shipping.templates.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Template Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Carrier</label>
                        <select class="form-select" name="carrier_id" id="carrier" required>
                            <option value="">Select</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <select class="form-select" name="service_id" id="service" required>
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Package Size</label>
                        <select class="form-select" name="package_size" required>
                            <option value="">Select</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_default" value="1">
                            <label class="form-check-label">Set as Default</label>
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
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTemplateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Template Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Carrier</label>
                        <select class="form-select" name="carrier_id" id="editCarrier" required>
                            <option value="">Select</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <select class="form-select" name="service_id" id="editService" required>
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Package Size</label>
                        <select class="form-select" name="package_size" required>
                            <option value="">Select</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_default" value="1">
                            <label class="form-check-label">Set as Default</label>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load services when carrier is selected
    $('#carrier, #editCarrier').change(function() {
        const carrierId = $(this).val();
        const serviceSelect = $(this).attr('id') === 'carrier' ? $('#service') : $('#editService');
        
        if (carrierId) {
            $.get(`/admin/shipping/carriers/${carrierId}/services`, function(data) {
                serviceSelect.empty().append('<option value="">Select</option>');
                data.forEach(service => {
                    serviceSelect.append(`<option value="${service.id}">${service.name}</option>`);
                });
            });
        } else {
            serviceSelect.empty().append('<option value="">Select</option>');
        }
    });

    // Edit template
    $('.edit-template').click(function() {
        const templateId = $(this).data('template');
        const form = $('#editTemplateForm');
        
        // Get template details via AJAX
        $.get(`/admin/shipping/templates/${templateId}/edit`, function(data) {
            form.attr('action', `/admin/shipping/templates/${templateId}`);
            form.find('[name="name"]').val(data.name);
            form.find('[name="carrier_id"]').val(data.carrier_id).trigger('change');
            form.find('[name="service_id"]').val(data.service_id);
            form.find('[name="package_size"]').val(data.package_size);
            form.find('[name="is_default"]').prop('checked', data.is_default);
            form.find('[name="is_active"]').prop('checked', data.is_active);
        });
    });

    // Delete template
    $('.delete-template').click(function() {
        const templateId = $(this).data('template');
        
        if (confirm('Are you sure you want to delete this template?')) {
            $.ajax({
                url: `/admin/shipping/templates/${templateId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection 