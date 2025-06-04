@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Shipping Labels</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" id="downloadAll">
                <i class="fas fa-download"></i> Download All
            </button>
            <button type="button" class="btn btn-danger" id="deleteAll">
                <i class="fas fa-trash"></i> Delete All
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th>Tracking Number</th>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labels as $label)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input label-checkbox" 
                                           value="{{ $label->id }}">
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.shipping.show', $label->shipment) }}" class="text-decoration-none">
                                    {{ $label->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $label->carrier->name }}</td>
                            <td>{{ $label->service->name }}</td>
                            <td>{{ $label->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $label->status_color }}">
                                    {{ $label->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.shipping.labels.download', $label) }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info view-label" 
                                            data-label="{{ $label->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-label" 
                                            data-label="{{ $label->id }}">
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
                {{ $labels->links() }}
            </div>
        </div>
    </div>
</div>

<!-- View Label Modal -->
<div class="modal fade" id="viewLabelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shipping Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="" alt="Shipping Label" class="img-fluid" id="labelImage">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-success" id="downloadLabel">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Select All Checkbox
    $('#selectAll').change(function() {
        $('.label-checkbox').prop('checked', $(this).prop('checked'));
    });

    // View Label
    $('.view-label').click(function() {
        const labelId = $(this).data('label');
        const modal = $('#viewLabelModal');
        
        $.get(`/admin/shipping/labels/${labelId}/preview`, function(response) {
            modal.find('#labelImage').attr('src', response.image_url);
            modal.find('#downloadLabel').attr('href', response.download_url);
            modal.modal('show');
        });
    });

    // Delete Label
    $('.delete-label').click(function() {
        const labelId = $(this).data('label');
        
        if (confirm('Are you sure you want to delete this shipping label?')) {
            $.ajax({
                url: `/admin/shipping/labels/${labelId}`,
                method: 'DELETE',
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

    // Download All
    $('#downloadAll').click(function() {
        const selectedLabels = $('.label-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedLabels.length === 0) {
            toastr.warning('Please select at least one label to download');
            return;
        }

        window.location.href = '{{ route("admin.shipping.labels.download-all") }}?ids=' + selectedLabels.join(',');
    });

    // Delete All
    $('#deleteAll').click(function() {
        const selectedLabels = $('.label-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedLabels.length === 0) {
            toastr.warning('Please select at least one label to delete');
            return;
        }

        if (confirm('Are you sure you want to delete the selected shipping labels?')) {
            $.ajax({
                url: '{{ route("admin.shipping.labels.delete-all") }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedLabels
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