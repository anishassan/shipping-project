@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipment Details</h1>
        <div class="btn-group">
            <a href="{{ route('customer.shipping.labels.download', $shipment) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download Label
            </a>
            <button type="button" class="btn btn-danger" id="cancelShipment">
                <i class="fas fa-times"></i> Cancel Shipment
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Shipment Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tracking History</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($shipment->tracking_history as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->status_color }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">{{ $history->status_text }}</h6>
                                <small class="text-muted">
                                    {{ $history->created_at->format('M d, Y H:i') }}
                                </small>
                                @if($history->location)
                                <p class="mb-0">{{ $history->location }}</p>
                                @endif
                                @if($history->description)
                                <p class="mb-0">{{ $history->description }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Shipment Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">From Address</h6>
                            <p class="mb-1">{{ $shipment->from_address->name }}</p>
                            <p class="mb-1">{{ $shipment->from_address->address_line1 }}</p>
                            @if($shipment->from_address->address_line2)
                            <p class="mb-1">{{ $shipment->from_address->address_line2 }}</p>
                            @endif
                            <p class="mb-1">
                                {{ $shipment->from_address->city }}, {{ $shipment->from_address->state }} {{ $shipment->from_address->postal_code }}
                            </p>
                            <p class="mb-0">{{ $shipment->from_address->country }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">To Address</h6>
                            <p class="mb-1">{{ $shipment->to_address->name }}</p>
                            <p class="mb-1">{{ $shipment->to_address->address_line1 }}</p>
                            @if($shipment->to_address->address_line2)
                            <p class="mb-1">{{ $shipment->to_address->address_line2 }}</p>
                            @endif
                            <p class="mb-1">
                                {{ $shipment->to_address->city }}, {{ $shipment->to_address->state }} {{ $shipment->to_address->postal_code }}
                            </p>
                            <p class="mb-0">{{ $shipment->to_address->country }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Package Details</h6>
                            <p class="mb-1">
                                <strong>Size:</strong> {{ ucfirst($shipment->package_size) }}
                            </p>
                            <p class="mb-1">
                                <strong>Weight:</strong> {{ $shipment->weight }} {{ $shipment->weight_unit }}
                            </p>
                            @if($shipment->dimensions)
                            <p class="mb-0">
                                <strong>Dimensions:</strong> {{ $shipment->dimensions }}
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Shipping Details</h6>
                            <p class="mb-1">
                                <strong>Carrier:</strong> {{ $shipment->carrier->name }}
                            </p>
                            <p class="mb-1">
                                <strong>Service:</strong> {{ $shipment->service->name }}
                            </p>
                            <p class="mb-1">
                                <strong>Cost:</strong> ${{ number_format($shipment->cost, 2) }}
                            </p>
                            <p class="mb-0">
                                <strong>Created:</strong> {{ $shipment->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-{{ $shipment->status_icon }} fa-3x text-{{ $shipment->status_color }}"></i>
                    </div>
                    <h5 class="card-title">{{ $shipment->status_text }}</h5>
                    <p class="text-muted mb-0">{{ $shipment->status_description }}</p>
                </div>
            </div>

            <!-- Tracking Number -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">Tracking Number</h6>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $shipment->tracking_number }}" readonly>
                        <button type="button" class="btn btn-outline-secondary copy-tracking" 
                                data-tracking="{{ $shipment->tracking_number }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estimated Delivery -->
            @if($shipment->estimated_delivery)
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">Estimated Delivery</h6>
                    <p class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ $shipment->estimated_delivery->format('M d, Y') }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Support -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Need Help?</h6>
                    <p class="text-muted mb-3">
                        If you have any questions about your shipment, please contact our support team.
                    </p>
                    <a href="{{ route('customer.support.create') }}" class="btn btn-primary w-100">
                        <i class="fas fa-headset"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Copy Tracking Number
    $('.copy-tracking').click(function() {
        const tracking = $(this).data('tracking');
        navigator.clipboard.writeText(tracking).then(function() {
            toastr.success('Tracking number copied to clipboard');
        });
    });

    // Cancel Shipment
    $('#cancelShipment').click(function() {
        if (confirm('Are you sure you want to cancel this shipment?')) {
            $.ajax({
                url: '{{ route("customer.shipping.cancel", $shipment) }}',
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