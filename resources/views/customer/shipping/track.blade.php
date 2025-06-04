@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Kargo Takip</h4>
                </div>

                <div class="card-body">
                    <form id="trackingForm" class="mb-4">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="tracking_number" 
                                   name="tracking_number" 
                                   placeholder="Takip numaranızı girin"
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Sorgula
                            </button>
                        </div>
                    </form>

                    <div id="trackingResult" class="d-none">
                        <div class="tracking-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Kargo Bilgileri</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Takip No:</th>
                                            <td id="result-tracking-number"></td>
                                        </tr>
                                        <tr>
                                            <th>Durum:</th>
                                            <td id="result-status"></td>
                                        </tr>
                                        <tr>
                                            <th>Kargo Firması:</th>
                                            <td id="result-carrier"></td>
                                        </tr>
                                        <tr>
                                            <th>Tahmini Teslimat:</th>
                                            <td id="result-delivery-date"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Adres Bilgileri</h5>
                                    <div id="result-addresses"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tracking-timeline">
                            <h5>Kargo Durumu</h5>
                            <div class="timeline" id="result-timeline">
                                <!-- Timeline items will be added here -->
                            </div>
                        </div>
                    </div>

                    <div id="trackingError" class="alert alert-danger d-none">
                        <!-- Error message will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: -20px;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-item:after {
    content: '';
    position: absolute;
    left: 10px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
}

.timeline-item.active:after {
    background: #28a745;
}

.timeline-item .time {
    font-size: 0.875rem;
    color: #6c757d;
}

.timeline-item .content {
    margin-top: 5px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#trackingForm').on('submit', function(e) {
        e.preventDefault();
        
        const trackingNumber = $('#tracking_number').val();
        
        $.ajax({
            url: '{{ route("customer.shipping.track-shipment") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tracking_number: trackingNumber
            },
            success: function(response) {
                if (response.success) {
                    showTrackingResult(response.shipment);
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Kargo bilgileri alınamadı. Lütfen tekrar deneyin.');
            }
        });
    });
});

function showTrackingResult(shipment) {
    // Hide error and show result
    $('#trackingError').addClass('d-none');
    $('#trackingResult').removeClass('d-none');

    // Update shipment info
    $('#result-tracking-number').text(shipment.tracking_number);
    $('#result-status').html(`<span class="badge bg-${shipment.status_color}">${shipment.status_text}</span>`);
    $('#result-carrier').text(shipment.rate.carrier);
    $('#result-delivery-date').text(shipment.tracker.estimated_delivery_date);

    // Update addresses
    const addresses = `
        <div class="mb-3">
            <strong>Gönderici:</strong><br>
            ${shipment.fromAddress.name}<br>
            ${shipment.fromAddress.street1}<br>
            ${shipment.fromAddress.city}, ${shipment.fromAddress.state} ${shipment.fromAddress.zip}
        </div>
        <div>
            <strong>Alıcı:</strong><br>
            ${shipment.toAddress.name}<br>
            ${shipment.toAddress.street1}<br>
            ${shipment.toAddress.city}, ${shipment.toAddress.state} ${shipment.toAddress.zip}
        </div>
    `;
    $('#result-addresses').html(addresses);

    // Update timeline
    const timeline = shipment.tracker.tracking_details.map((detail, index) => `
        <div class="timeline-item ${index === 0 ? 'active' : ''}">
            <div class="time">${detail.datetime}</div>
            <div class="content">${detail.message}</div>
        </div>
    `).join('');
    $('#result-timeline').html(timeline);
}

function showError(message) {
    $('#trackingResult').addClass('d-none');
    $('#trackingError').removeClass('d-none').text(message);
}
</script>
@endpush
@endsection 