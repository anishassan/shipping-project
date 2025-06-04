@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipment Details</h1>
        <div class="btn-group">
            <a href="{{ route('admin.shipping.edit', $shipment) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" class="btn btn-danger" id="deleteShipment">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Shipment Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">Tracking Number</h5>
                            <h3 class="mb-0">{{ $shipment->tracking_number }}</h3>
                        </div>
                        <div class="text-end">
                            <h5 class="card-title mb-1">Status</h5>
                            <span class="badge bg-{{ $shipment->status_color }} fs-6">
                                {{ $shipment->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">From Address</h5>
                            <p class="mb-1">{{ $shipment->from_name }}</p>
                            <p class="mb-1">{{ $shipment->from_address }}</p>
                            <p class="mb-1">{{ $shipment->from_city }}, {{ $shipment->from_state }} {{ $shipment->from_postal_code }}</p>
                            <p class="mb-1">{{ $shipment->from_country }}</p>
                            <p class="mb-0">{{ $shipment->from_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">To Address</h5>
                            <p class="mb-1">{{ $shipment->to_name }}</p>
                            <p class="mb-1">{{ $shipment->to_address }}</p>
                            <p class="mb-1">{{ $shipment->to_city }}, {{ $shipment->to_state }} {{ $shipment->to_postal_code }}</p>
                            <p class="mb-1">{{ $shipment->to_country }}</p>
                            <p class="mb-0">{{ $shipment->to_phone }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Package Details</h5>
                            <p class="mb-1"><strong>Weight:</strong> {{ $shipment->weight }} {{ $shipment->weight_unit }}</p>
                            <p class="mb-1"><strong>Dimensions:</strong> {{ $shipment->length }}x{{ $shipment->width }}x{{ $shipment->height }} {{ $shipment->dimension_unit }}</p>
                            <p class="mb-0"><strong>Package Type:</strong> {{ $shipment->package_type }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Shipping Details</h5>
                            <p class="mb-1"><strong>Carrier:</strong> {{ $shipment->carrier->name }}</p>
                            <p class="mb-1"><strong>Service:</strong> {{ $shipment->service->name }}</p>
                            <p class="mb-1"><strong>Cost:</strong> ${{ number_format($shipment->cost, 2) }}</p>
                            <p class="mb-0"><strong>Created At:</strong> {{ $shipment->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tracking History</h5>
                    <div class="timeline">
                        @foreach($shipment->tracking_history as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->status_color }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $history->status_text }}</h6>
                                <p class="mb-1">{{ $history->description }}</p>
                                <small class="text-muted">{{ $history->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Current Status</h5>
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $shipment->status_color }} fs-5">
                            {{ $shipment->status_text }}
                        </span>
                    </div>
                    <p class="mb-0 text-center">{{ $shipment->status_description }}</p>
                </div>
            </div>

            <!-- Estimated Delivery -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Estimated Delivery</h5>
                    <div class="text-center">
                        <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                        <h4>{{ $shipment->estimated_delivery_date->format('M d, Y') }}</h4>
                        <p class="mb-0 text-muted">
                            {{ $shipment->estimated_delivery_date->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.shipping.labels.download', $shipment->label) }}" 
                           class="btn btn-success">
                            <i class="fas fa-download"></i> Download Label
                        </a>
                        <button type="button" class="btn btn-info" id="updateStatus">
                            <i class="fas fa-sync"></i> Update Status
                        </button>
                        <button type="button" class="btn btn-warning" id="editShipment">
                            <i class="fas fa-edit"></i> Edit Shipment
                        </button>
                        <button type="button" class="btn btn-danger" id="cancelShipment">
                            <i class="fas fa-times"></i> Cancel Shipment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Shipment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" action="{{ route('admin.shipping.update-status', $shipment) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_transit">In Transit</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="failed">Failed</option>
                            <option value="returned">Returned</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Update Status
    $('#updateStatus').click(function() {
        $('#updateStatusModal').modal('show');
    });

    // Delete Shipment
    $('#deleteShipment').click(function() {
        if (confirm('Are you sure you want to delete this shipment?')) {
            $.ajax({
                url: '{{ route("admin.shipping.destroy", $shipment) }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    window.location.href = '{{ route("admin.shipping.index") }}';
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }
    });

    // Cancel Shipment
    $('#cancelShipment').click(function() {
        if (confirm('Are you sure you want to cancel this shipment?')) {
            $.ajax({
                url: '{{ route("admin.shipping.cancel", $shipment) }}',
                method: 'POST',
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