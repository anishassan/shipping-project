@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Webhook Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWebhookModal">
            <i class="fas fa-plus"></i> Add New Webhook
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Webhook URL</th>
                            <th>Events</th>
                            <th>Last Triggered</th>
                            <th>Success/Failure</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($webhooks as $webhook)
                        <tr>
                            <td>{{ $webhook->url }}</td>
                            <td>
                                @foreach($webhook->events as $event)
                                    <span class="badge bg-info me-1">{{ $event }}</span>
                                @endforeach
                            </td>
                            <td>{{ $webhook->last_triggered_at ? $webhook->last_triggered_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                <span class="text-success">{{ $webhook->success_count }}</span> /
                                <span class="text-danger">{{ $webhook->failure_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $webhook->is_active ? 'success' : 'danger' }}">
                                    {{ $webhook->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info edit-webhook" 
                                            data-webhook="{{ $webhook->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editWebhookModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success test-webhook" 
                                            data-webhook="{{ $webhook->id }}">
                                        <i class="fas fa-vial"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-webhook" 
                                            data-webhook="{{ $webhook->id }}">
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
                {{ $webhooks->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Webhook Modal -->
<div class="modal fade" id="addWebhookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addWebhookForm" action="{{ route('admin.shipping.webhooks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Webhook URL</label>
                        <input type="url" class="form-control" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Events</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.created">
                                    <label class="form-check-label">Shipment Created</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.updated">
                                    <label class="form-check-label">Shipment Updated</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.deleted">
                                    <label class="form-check-label">Shipment Deleted</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.status_changed">
                                    <label class="form-check-label">Status Changed</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.delivered">
                                    <label class="form-check-label">Delivered</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.failed">
                                    <label class="form-check-label">Delivery Failed</label>
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
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Webhook Modal -->
<div class="modal fade" id="editWebhookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editWebhookForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Webhook URL</label>
                        <input type="url" class="form-control" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Events</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.created">
                                    <label class="form-check-label">Shipment Created</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.updated">
                                    <label class="form-check-label">Shipment Updated</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.deleted">
                                    <label class="form-check-label">Shipment Deleted</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.status_changed">
                                    <label class="form-check-label">Status Changed</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.delivered">
                                    <label class="form-check-label">Delivered</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="events[]" value="shipment.failed">
                                    <label class="form-check-label">Delivery Failed</label>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Edit webhook
    $('.edit-webhook').click(function() {
        const webhookId = $(this).data('webhook');
        const form = $('#editWebhookForm');
        
        // Get webhook details via AJAX
        $.get(`/admin/shipping/webhooks/${webhookId}/edit`, function(data) {
            form.attr('action', `/admin/shipping/webhooks/${webhookId}`);
            form.find('[name="url"]').val(data.url);
            form.find('[name="is_active"]').prop('checked', data.is_active);
            
            // Check events
            form.find('[name="events[]"]').prop('checked', false);
            data.events.forEach(event => {
                form.find(`[name="events[]"][value="${event}"]`).prop('checked', true);
            });
        });
    });

    // Test webhook
    $('.test-webhook').click(function() {
        const webhookId = $(this).data('webhook');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.post(`/admin/shipping/webhooks/${webhookId}/test`, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success('Webhook test successful');
            } else {
                toastr.error(response.message || 'Webhook test failed');
            }
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-vial"></i>');
        });
    });

    // Delete webhook
    $('.delete-webhook').click(function() {
        const webhookId = $(this).data('webhook');
        
        if (confirm('Are you sure you want to delete this webhook?')) {
            $.ajax({
                url: `/admin/shipping/webhooks/${webhookId}`,
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