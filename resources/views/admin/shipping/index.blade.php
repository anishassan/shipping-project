@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipments</h1>
        <a href="{{ route('admin.shipping.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Shipment
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Carrier</label>
                        <select class="form-select" id="carrierFilter">
                            <option value="">All Carriers</option>
                            @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Date Range</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="Select date range">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search tracking number, reference...">
                    </div>
                </div>
            </div>

            <!-- Shipments Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Reference</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Carrier</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $shipment)
                        <tr>
                            <td>
                                <a href="{{ route('admin.shipping.show', $shipment->id) }}">
                                    {{ $shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $shipment->reference_number }}</td>
                            <td>{{ $shipment->from_city }}, {{ $shipment->from_country }}</td>
                            <td>{{ $shipment->to_city }}, {{ $shipment->to_country }}</td>
                            <td>{{ $shipment->carrier->name }}</td>
                            <td>
                                <span class="badge bg-{{ $shipment->status_color }}">
                                    {{ $shipment->status }}
                                </span>
                            </td>
                            <td>{{ $shipment->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.shipping.show', $shipment->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.shipping.edit', $shipment->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteShipment({{ $shipment->id }})"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $shipments->firstItem() }} to {{ $shipments->lastItem() }} of {{ $shipments->total() }} entries
                </div>
                <div>
                    {{ $shipments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize date range picker
    $('#dateRange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        filterShipments();
    });

    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        filterShipments();
    });

    // Filter change handlers
    $('#statusFilter, #carrierFilter').change(filterShipments);
    $('#searchInput').on('keyup', debounce(filterShipments, 500));

    function filterShipments() {
        const filters = {
            status: $('#statusFilter').val(),
            carrier: $('#carrierFilter').val(),
            dateRange: $('#dateRange').val(),
            search: $('#searchInput').val()
        };

        $.get('{{ route("admin.shipping.index") }}', filters, function(response) {
            $('tbody').html(response.html);
            updatePagination(response.pagination);
        });
    }

    function updatePagination(pagination) {
        $('.pagination').html(pagination);
    }
});

function deleteShipment(id) {
    if (confirm('Are you sure you want to delete this shipment?')) {
        $.ajax({
            url: `/admin/shipping/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success('Shipment deleted successfully');
                filterShipments();
            },
            error: function() {
                toastr.error('An error occurred while deleting the shipment');
            }
        });
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
@endsection 