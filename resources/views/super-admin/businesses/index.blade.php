@extends('layouts.super-admin')

@section('content')
@php
$states = ['Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Business Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBusinessModal">
        <i class="fas fa-plus me-2"></i>Register New Business
    </button>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Database</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($businesses as $business)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $business->business_name }}</div>
                        <small class="text-muted">Registered {{ $business->created_at->format('d M Y') }}</small>
                    </td>
                    <td>{{ $business->owner_name }}</td>
                    <td>{{ $business->email }}</td>
                    <td><code class="bg-light px-2 py-1 rounded">{{ $business->database_name }}</code></td>
                    <td>
                        <span class="badge {{ $business->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($business->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item edit-business" data-business="{{ json_encode($business) }}">
                                        <i class="fas fa-edit me-2"></i>Edit Details
                                    </button>
                                </li>
                                <li>
                                    <form action="{{ route('super-admin.businesses.toggle-status', $business->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas {{ $business->status === 'active' ? 'fa-pause' : 'fa-play' }} me-2"></i>
                                            {{ $business->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('super-admin.businesses.destroy', $business->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete Business
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No businesses registered yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Business Modal -->
<div class="modal fade" id="editBusinessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Business Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBusinessForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" id="edit_business_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner Name</label>
                        <input type="text" name="owner_name" id="edit_owner_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Number (Optional)</label>
                        <input type="text" name="gst_number" id="edit_gst_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <select name="state" id="edit_state" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Business</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Business Modal -->
<div class="modal fade" id="createBusinessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register New Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.businesses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control" placeholder="e.g. Awesome Store" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner Name</label>
                        <input type="text" name="owner_name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. john@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. +1234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Number (Optional)</label>
                        <input type="text" name="gst_number" class="form-control" placeholder="e.g. 22AAAAA0000A1Z5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Admin Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Business</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.edit-business').on('click', function() {
            let business = $(this).data('business');
            let url = "{{ route('super-admin.businesses.update', ':id') }}".replace(':id', business.id);
            
            $('#editBusinessForm').attr('action', url);
            $('#edit_business_name').val(business.business_name);
            $('#edit_owner_name').val(business.owner_name);
            $('#edit_email').val(business.email);
            $('#edit_phone').val(business.phone);
            $('#edit_gst_number').val(business.gst_number);
            $('#edit_state').val(business.state);
            
            $('#editBusinessModal').modal('show');
        });

        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Removing this business record will disable their access. Data in their database will remain intact.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
