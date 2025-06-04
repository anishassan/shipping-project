@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Statistics</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary active" data-period="today">Today</button>
            <button type="button" class="btn btn-outline-secondary" data-period="week">This Week</button>
            <button type="button" class="btn btn-outline-secondary" data-period="month">This Month</button>
            <button type="button" class="btn btn-outline-secondary" data-period="year">This Year</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Shipments</h5>
                    <h2 class="mb-0">{{ $statistics['total_shipments'] }}</h2>
                    <small class="text-white-50">
                        {{ $statistics['shipment_change'] > 0 ? '+' : '' }}{{ $statistics['shipment_change'] }}% from previous period
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Delivered</h5>
                    <h2 class="mb-0">{{ $statistics['delivered_shipments'] }}</h2>
                    <small class="text-white-50">
                        {{ $statistics['delivered_change'] > 0 ? '+' : '' }}{{ $statistics['delivered_change'] }}% from previous period
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">In Transit</h5>
                    <h2 class="mb-0">{{ $statistics['in_transit_shipments'] }}</h2>
                    <small class="text-white-50">
                        {{ $statistics['in_transit_change'] > 0 ? '+' : '' }}{{ $statistics['in_transit_change'] }}% from previous period
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Failed</h5>
                    <h2 class="mb-0">{{ $statistics['failed_shipments'] }}</h2>
                    <small class="text-white-50">
                        {{ $statistics['failed_change'] > 0 ? '+' : '' }}{{ $statistics['failed_change'] }}% from previous period
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Shipments by Status</h5>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Shipments by Carrier</h5>
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Carriers -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Top Carriers</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Total Shipments</th>
                            <th>Success Rate</th>
                            <th>Average Delivery Time</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statistics['top_carriers'] as $carrier)
                        <tr>
                            <td>{{ $carrier->name }}</td>
                            <td>{{ $carrier->total_shipments }}</td>
                            <td>{{ $carrier->success_rate }}%</td>
                            <td>{{ $carrier->avg_delivery_time }} days</td>
                            <td>${{ number_format($carrier->revenue, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Period Filter
    $('.btn-group .btn').click(function() {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        
        const period = $(this).data('period');
        loadStatistics(period);
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Delivered', 'In Transit', 'Failed'],
            datasets: [{
                data: [
                    {{ $statistics['delivered_shipments'] }},
                    {{ $statistics['in_transit_shipments'] }},
                    {{ $statistics['failed_shipments'] }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Carrier Chart
    const carrierCtx = document.getElementById('carrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($statistics['top_carriers']->pluck('name')) !!},
            datasets: [{
                label: 'Shipments',
                data: {!! json_encode($statistics['top_carriers']->pluck('total_shipments')) !!},
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function loadStatistics(period) {
        $.get(`/admin/shipping/statistics?period=${period}`, function(data) {
            // Update summary cards
            $('.card-title:contains("Total Shipments")').next('h2').text(data.total_shipments);
            $('.card-title:contains("Delivered")').next('h2').text(data.delivered_shipments);
            $('.card-title:contains("In Transit")').next('h2').text(data.in_transit_shipments);
            $('.card-title:contains("Failed")').next('h2').text(data.failed_shipments);

            // Update charts
            statusChart.data.datasets[0].data = [
                data.delivered_shipments,
                data.in_transit_shipments,
                data.failed_shipments
            ];
            statusChart.update();

            carrierChart.data.labels = data.top_carriers.map(c => c.name);
            carrierChart.data.datasets[0].data = data.top_carriers.map(c => c.total_shipments);
            carrierChart.update();

            // Update top carriers table
            const tbody = $('.table tbody');
            tbody.empty();
            data.top_carriers.forEach(carrier => {
                tbody.append(`
                    <tr>
                        <td>${carrier.name}</td>
                        <td>${carrier.total_shipments}</td>
                        <td>${carrier.success_rate}%</td>
                        <td>${carrier.avg_delivery_time} days</td>
                        <td>$${carrier.revenue.toFixed(2)}</td>
                    </tr>
                `);
            });
        });
    }
});
</script>
@endpush
@endsection 