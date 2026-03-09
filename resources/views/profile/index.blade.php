@extends($layout)

@section('content')
@php
$states = ['Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'];
@endphp
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary">Personal Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route($routePrefix . '.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted text-uppercase fw-bold">Profile Photo</label>
                                <input type="file" name="profile_photo" class="form-control">
                                <div class="form-text mt-1 small text-muted">Allowed: JPG, PNG, GIF (Max: 2MB)</div>
                            </div>
                            
                            <hr class="my-4 text-muted opacity-25">
                            
                            <h6 class="fw-bold mb-3 text-secondary">Change Password <small class="fw-normal text-muted">(Leave blank to keep current)</small></h6>
                            
                            <div class="col-md-12">
                                <label class="form-label small text-muted text-uppercase fw-bold">Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Required for password changes">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control">
                            </div>
                            
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($user->role === 'business_admin' && $business)
            <!-- Business Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary">Business Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route($routePrefix . '.profile.updateBusiness') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small text-muted text-uppercase fw-bold">Business Name</label>
                                <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $business->business_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">Owner Name</label>
                                <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $business->owner_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $business->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">GST Number</label>
                                <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $business->gst_number) }}" placeholder="e.g. 22AAAAA0000A1Z5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold">State</label>
                                <select name="state" class="form-select" required>
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state }}" {{ old('state', $business->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4">Update Business Details</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Account Overview -->
            <div class="card shadow-sm border-0 bg-primary text-white text-center p-4 h-100">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-inline-block bg-white text-primary rounded-circle overflow-hidden mb-3 shadow" style="width: 120px; height: 120px;">
                            @if($user->profile_photo)
                                <img src="{{ asset($user->profile_photo) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                        <div class="opacity-75 small">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                    
                    <hr class="bg-white opacity-25">
                    
                    <div class="text-start mt-4">
                        <div class="mb-3">
                            <label class="opacity-75 small d-block mb-1">Joined Date</label>
                            <div class="fw-semibold">{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                        @if($user->role === 'business_admin')
                        <div class="mb-3">
                            <label class="opacity-75 small d-block mb-1">Status</label>
                            <span class="badge bg-success">ACTIVE</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
