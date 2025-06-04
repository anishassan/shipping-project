@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping History</h1>
        <a href="{{ route('customer.shipping.create') }}" class="btn btn-primary">
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
                                <a href="{{ route('customer.shipping.show', $shipment->id) }}">
                                    {{ $shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $shipment->reference_number }}</td>
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
                                    <a href="{{ route('customer.shipping.show', $shipment->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($shipment->status === 'pending')
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="cancelShipment({{ $shipment->id }})"
                                            title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
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

        $.get('{{ route("customer.shipping.history") }}', filters, function(response) {
            $('tbody').html(response.html);
            updatePagination(response.pagination);
        });
    }

    function updatePagination(pagination) {
        $('.pagination').html(pagination);
    }
});

function cancelShipment(id) {
    if (confirm('Are you sure you want to cancel this shipment?')) {
        $.ajax({
            url: `/customer/shipping/${id}/cancel`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success('Shipment cancelled successfully');
                filterShipments();
            },
            error: function() {
                toastr.error('An error occurred while cancelling the shipment');
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