@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Logs</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" id="exportLogs">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button type="button" class="btn btn-danger" id="clearLogs">
                <i class="fas fa-trash"></i> Clear Logs
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Log Type</label>
                    <select class="form-select" id="logType">
                        <option value="">All</option>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Carrier</label>
                    <select class="form-select" id="carrier">
                        <option value="">All</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="startDate">
                        <span class="input-group-text">-</span>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-primary w-100" id="filterLogs">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Log Type</th>
                            <th>Carrier</th>
                            <th>Message</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>
                                <span class="badge bg-{{ $log->type_color }}">
                                    {{ $log->type_text }}
                                </span>
                            </td>
                            <td>{{ $log->carrier->name ?? '-' }}</td>
                            <td>{{ $log->message }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-details" 
                                        data-log="{{ $log->id }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#logDetailsModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger delete-log" 
                                        data-log="{{ $log->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <p id="logMessage" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Details</label>
                    <pre id="logDetails" class="form-control-plaintext bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stack Trace</label>
                    <pre id="logStackTrace" class="form-control-plaintext bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // View log details
    $('.view-details').click(function() {
        const logId = $(this).data('log');
        
        $.get(`/admin/shipping/logs/${logId}`, function(data) {
            $('#logMessage').text(data.message);
            $('#logDetails').text(JSON.stringify(data.details, null, 2));
            $('#logStackTrace').text(data.stack_trace || 'No stack trace available');
        });
    });

    // Delete log
    $('.delete-log').click(function() {
        const logId = $(this).data('log');
        
        if (confirm('Are you sure you want to delete this log entry?')) {
            $.ajax({
                url: `/admin/shipping/logs/${logId}`,
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

    // Filter logs
    $('#filterLogs').click(function() {
        const params = {
            type: $('#logType').val(),
            carrier: $('#carrier').val(),
            start_date: $('#startDate').val(),
            end_date: $('#endDate').val()
        };
        
        window.location.href = '{{ route("admin.shipping.logs.index") }}?' + $.param(params);
    });

    // Export to Excel
    $('#exportLogs').click(function() {
        const params = {
            type: $('#logType').val(),
            carrier: $('#carrier').val(),
            start_date: $('#startDate').val(),
            end_date: $('#endDate').val(),
            export: true
        };
        
        window.location.href = '{{ route("admin.shipping.logs.index") }}?' + $.param(params);
    });

    // Clear logs
    $('#clearLogs').click(function() {
        if (confirm('Are you sure you want to delete all log entries? This action cannot be undone!')) {
            $.ajax({
                url: '{{ route("admin.shipping.logs.clear") }}',
                type: 'POST',
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