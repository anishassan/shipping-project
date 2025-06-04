@extends('layouts.customer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Address Book</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="fas fa-plus"></i> Add New Address
        </button>
    </div>

    <div class="row">
        @foreach($addresses as $address)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $address->name }}</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-warning edit-address" 
                                    data-address="{{ $address->id }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editAddressModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-address" 
                                    data-address="{{ $address->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1">
                            <strong>Contact:</strong> {{ $address->contact_name }}
                        </p>
                        <p class="mb-1">
                            <strong>Phone:</strong> {{ $address->phone }}
                        </p>
                        <p class="mb-1">
                            <strong>Email:</strong> {{ $address->email }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1">{{ $address->address_line1 }}</p>
                        @if($address->address_line2)
                        <p class="mb-1">{{ $address->address_line2 }}</p>
                        @endif
                        <p class="mb-1">
                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                        </p>
                        <p class="mb-0">{{ $address->country }}</p>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input set-default-address" 
                               data-address="{{ $address->id }}"
                               {{ $address->is_default ? 'checked' : '' }}>
                        <label class="form-check-label">Set as Default Address</label>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        @if($addresses->isEmpty())
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                <h5>No addresses found</h5>
                <p class="text-muted">Add your first shipping address to get started.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm" action="{{ route('customer.shipping.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Address Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Name</label>
                        <input type="text" class="form-control" name="contact_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select class="form-select" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAddressForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Address Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Name</label>
                        <input type="text" class="form-control" name="contact_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select class="form-select" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Edit Address
    $('.edit-address').click(function() {
        const addressId = $(this).data('address');
        const form = $('#editAddressForm');
        
        $.get(`/customer/shipping/addresses/${addressId}/edit`, function(data) {
            form.attr('action', `/customer/shipping/addresses/${addressId}`);
            form.find('[name="name"]').val(data.name);
            form.find('[name="contact_name"]').val(data.contact_name);
            form.find('[name="phone"]').val(data.phone);
            form.find('[name="email"]').val(data.email);
            form.find('[name="address_line1"]').val(data.address_line1);
            form.find('[name="address_line2"]').val(data.address_line2);
            form.find('[name="city"]').val(data.city);
            form.find('[name="state"]').val(data.state);
            form.find('[name="postal_code"]').val(data.postal_code);
            form.find('[name="country"]').val(data.country);
        });
    });

    // Delete Address
    $('.delete-address').click(function() {
        const addressId = $(this).data('address');
        
        if (confirm('Are you sure you want to delete this address?')) {
            $.ajax({
                url: `/customer/shipping/addresses/${addressId}`,
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

    // Set Default Address
    $('.set-default-address').change(function() {
        const addressId = $(this).data('address');
        const isChecked = $(this).prop('checked');
        
        $.ajax({
            url: `/customer/shipping/addresses/${addressId}/default`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                is_default: isChecked
            },
            success: function() {
                if (isChecked) {
                    $('.set-default-address').not(this).prop('checked', false);
                }
                toastr.success('Default address updated successfully');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
                $(this).prop('checked', !isChecked);
            }
        });
    });
});
</script>
@endpush
@endsection 