@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Carriers</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarrierModal">
            <i class="fas fa-plus"></i> Add New Carrier
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Carrier Name</th>
                            <th>Code</th>
                            <th>Website</th>
                            <th>API Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carriers as $carrier)
                        <tr>
                            <td>{{ $carrier->name }}</td>
                            <td>{{ $carrier->code }}</td>
                            <td>
                                <a href="{{ $carrier->website }}" target="_blank" class="text-decoration-none">
                                    {{ $carrier->website }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-{{ $carrier->api_status_color }}">
                                    {{ $carrier->api_status_text }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $carrier->is_active ? 'success' : 'danger' }}">
                                    {{ $carrier->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.shipping.carrier-services.index', $carrier) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning edit-carrier" 
                                            data-carrier="{{ $carrier->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCarrierModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success test-api" 
                                            data-carrier="{{ $carrier->id }}">
                                        <i class="fas fa-vial"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-carrier" 
                                            data-carrier="{{ $carrier->id }}">
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
                {{ $carriers->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Carrier Modal -->
<div class="modal fade" id="addCarrierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Carrier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCarrierForm" action="{{ route('admin.shipping.carriers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Carrier Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" name="website" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" class="form-control" name="api_secret">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API URL</label>
                        <input type="url" class="form-control" name="api_url">
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

<!-- Edit Carrier Modal -->
<div class="modal fade" id="editCarrierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Carrier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCarrierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Carrier Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" name="website" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" class="form-control" name="api_secret">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API URL</label>
                        <input type="url" class="form-control" name="api_url">
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
    // Edit carrier
    $('.edit-carrier').click(function() {
        const carrierId = $(this).data('carrier');
        const form = $('#editCarrierForm');
        
        // Get carrier details via AJAX
        $.get(`/admin/shipping/carriers/${carrierId}/edit`, function(data) {
            form.attr('action', `/admin/shipping/carriers/${carrierId}`);
            form.find('[name="name"]').val(data.name);
            form.find('[name="code"]').val(data.code);
            form.find('[name="website"]').val(data.website);
            form.find('[name="api_key"]').val(data.api_key);
            form.find('[name="api_url"]').val(data.api_url);
            form.find('[name="is_active"]').prop('checked', data.is_active);
        });
    });

    // Test API
    $('.test-api').click(function() {
        const carrierId = $(this).data('carrier');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.post(`/admin/shipping/carriers/${carrierId}/test-api`, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success('API connection successful');
            } else {
                toastr.error(response.message || 'API connection failed');
            }
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-vial"></i>');
        });
    });

    // Delete carrier
    $('.delete-carrier').click(function() {
        const carrierId = $(this).data('carrier');
        
        if (confirm('Are you sure you want to delete this carrier?')) {
            $.ajax({
                url: `/admin/shipping/carriers/${carrierId}`,
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