@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Reports</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button type="button" class="btn btn-danger" id="exportPdf">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Shipments</h5>
                    <h2 class="mb-0">{{ $totalShipments }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Delivered</h5>
                    <h2 class="mb-0">{{ $deliveredShipments }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">In Transit</h5>
                    <h2 class="mb-0">{{ $inTransitShipments }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Failed</h5>
                    <h2 class="mb-0">{{ $failedShipments }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipments by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipments by Carrier</h5>
                </div>
                <div class="card-body">
                    <canvas id="carrierChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Detailed Report</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Delivered At</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $shipment)
                        <tr>
                            <td>
                                <a href="{{ route('customer.shipping.show', $shipment) }}" class="text-decoration-none">
                                    {{ $shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $shipment->carrier->name }}</td>
                            <td>{{ $shipment->service->name }}</td>
                            <td>
                                <span class="badge bg-{{ $shipment->status_color }}">
                                    {{ $shipment->status_text }}
                                </span>
                            </td>
                            <td>{{ $shipment->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>${{ number_format($shipment->cost, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $shipments->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($statusLabels) !!},
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
                ]
            }]
        }
    });

    // Carrier Chart
    const carrierCtx = document.getElementById('carrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($carrierLabels) !!},
            datasets: [{
                label: 'Shipments',
                data: {!! json_encode($carrierData) !!},
                backgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Export to Excel
    $('#exportExcel').click(function() {
        window.location.href = '{{ route("customer.shipping.reports.export", ["format" => "excel"]) }}';
    });

    // Export to PDF
    $('#exportPdf').click(function() {
        window.location.href = '{{ route("customer.shipping.reports.export", ["format" => "pdf"]) }}';
    });
});
</script>
@endpush
@endsection 