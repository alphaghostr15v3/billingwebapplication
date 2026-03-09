@extends('layouts.super-admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Subscription Plans</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
        <i class="fas fa-plus me-2"></i>Create New Plan
    </button>
</div>

<div class="row g-4">
    @foreach($subscriptions as $plan)
    <div class="col-md-4">
        <div class="card h-100 p-4 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h4 class="fw-bold mb-0 text-primary">{{ $plan->plan_name }}</h4>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <form action="{{ route('super-admin.subscriptions.destroy', $plan->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">Delete Plan</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mb-4">
                <span class="display-6 fw-bold">₹{{ number_format($plan->price, 0) }}</span>
                <span class="text-muted">/ {{ $plan->duration_days }} Days</span>
            </div>
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Full Access to Dashboard</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Unlimited Invoices</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Priority Support</li>
            </ul>
        </div>
    </div>
    @endforeach

    @if($subscriptions->isEmpty())
    <div class="col-12 text-center py-5">
        <div class="text-muted mb-3"><i class="fas fa-layer-group fs-1"></i></div>
        <h5 class="text-muted">No subscription plans created yet.</h5>
    </div>
    @endif
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="plan_name" class="form-control" placeholder="e.g. Basic, Premium, Gold" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Days)</label>
                        <input type="number" name="duration_days" class="form-control" value="30" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
