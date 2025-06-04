@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">API Integrations</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIntegrationModal">
            <i class="fas fa-plus"></i> Add New Integration
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Integration Name</th>
                            <th>API Provider</th>
                            <th>API Version</th>
                            <th>Last Check</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($integrations as $integration)
                        <tr>
                            <td>{{ $integration->name }}</td>
                            <td>{{ $integration->provider }}</td>
                            <td>{{ $integration->api_version }}</td>
                            <td>{{ $integration->last_check_at ? $integration->last_check_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $integration->status_color }}">
                                    {{ $integration->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info edit-integration" 
                                            data-integration="{{ $integration->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editIntegrationModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success test-integration" 
                                            data-integration="{{ $integration->id }}">
                                        <i class="fas fa-vial"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-integration" 
                                            data-integration="{{ $integration->id }}">
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
                {{ $integrations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Integration Modal -->
<div class="modal fade" id="addIntegrationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New API Integration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addIntegrationForm" action="{{ route('admin.shipping.api-integrations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Integration Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Provider</label>
                        <select class="form-select" name="provider" required>
                            <option value="">Select</option>
                            <option value="ups">UPS</option>
                            <option value="fedex">FedEx</option>
                            <option value="dhl">DHL</option>
                            <option value="usps">USPS</option>
                            <option value="amazon">Amazon Shipping</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Version</label>
                        <input type="text" class="form-control" name="api_version" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" class="form-control" name="api_secret" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API URL</label>
                        <input type="url" class="form-control" name="api_url" required>
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

<!-- Edit Integration Modal -->
<div class="modal fade" id="editIntegrationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit API Integration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editIntegrationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Integration Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Provider</label>
                        <select class="form-select" name="provider" required>
                            <option value="">Select</option>
                            <option value="ups">UPS</option>
                            <option value="fedex">FedEx</option>
                            <option value="dhl">DHL</option>
                            <option value="usps">USPS</option>
                            <option value="amazon">Amazon Shipping</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Version</label>
                        <input type="text" class="form-control" name="api_version" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" class="form-control" name="api_secret" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API URL</label>
                        <input type="url" class="form-control" name="api_url" required>
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
    // Edit integration
    $('.edit-integration').click(function() {
        const integrationId = $(this).data('integration');
        const form = $('#editIntegrationForm');
        
        // Get integration details via AJAX
        $.get(`/admin/shipping/api-integrations/${integrationId}/edit`, function(data) {
            form.attr('action', `/admin/shipping/api-integrations/${integrationId}`);
            form.find('[name="name"]').val(data.name);
            form.find('[name="provider"]').val(data.provider);
            form.find('[name="api_version"]').val(data.api_version);
            form.find('[name="api_key"]').val(data.api_key);
            form.find('[name="api_url"]').val(data.api_url);
            form.find('[name="is_active"]').prop('checked', data.is_active);
        });
    });

    // Test integration
    $('.test-integration').click(function() {
        const integrationId = $(this).data('integration');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.post(`/admin/shipping/api-integrations/${integrationId}/test`, {
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

    // Delete integration
    $('.delete-integration').click(function() {
        const integrationId = $(this).data('integration');
        
        if (confirm('Are you sure you want to delete this API integration?')) {
            $.ajax({
                url: `/admin/shipping/api-integrations/${integrationId}`,
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