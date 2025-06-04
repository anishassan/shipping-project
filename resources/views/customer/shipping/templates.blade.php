@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipping Templates</h5>
                </div>

                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="row">
                            @foreach($templates as $template)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $template->name }}</h5>
                                            <p class="card-text">
                                                <strong>Carrier:</strong> {{ $template->carrier->name }}<br>
                                                <strong>Service:</strong> {{ $template->service->name }}<br>
                                                <strong>Package Size:</strong> {{ $template->package_size }}<br>
                                                <strong>Default:</strong> 
                                                <span class="badge bg-{{ $template->is_default ? 'success' : 'secondary' }}">
                                                    {{ $template->is_default ? 'Yes' : 'No' }}
                                                </span>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-primary use-template" 
                                                        data-template="{{ $template->id }}">
                                                    <i class="fas fa-shipping-fast"></i> Use Template
                                                </button>
                                                @if(!$template->is_default)
                                                    <button type="button" class="btn btn-secondary set-default" 
                                                            data-template="{{ $template->id }}">
                                                        <i class="fas fa-star"></i> Set as Default
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $templates->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            No shipping templates available. Please contact your administrator to create templates.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Use template
    $('.use-template').click(function() {
        const templateId = $(this).data('template');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Using...');
        
        $.post(`/customer/shipping/templates/${templateId}/use`, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success('Template applied successfully');
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            } else {
                toastr.error(response.message || 'Failed to apply template');
            }
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-shipping-fast"></i> Use Template');
        });
    });

    // Set as default
    $('.set-default').click(function() {
        const templateId = $(this).data('template');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Setting...');
        
        $.post(`/customer/shipping/templates/${templateId}/default`, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success('Default template updated successfully');
                location.reload();
            } else {
                toastr.error(response.message || 'Failed to update default template');
            }
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-star"></i> Set as Default');
        });
    });
});
</script>
@endpush
@endsection 