@extends('layouts.super-admin')

@section('content')
<h2 class="fw-bold mb-4">Super Admin Dashboard</h2>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4 border-start border-primary border-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="fas fa-briefcase text-primary fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Businesses</h6>
                    <h3 class="fw-bold mb-0">{{ \App\Models\Business::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-start border-success border-4">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="fas fa-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Active Subscriptions</h6>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-start border-warning border-4">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="fas fa-clock text-warning fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Pending Requests</h6>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold mb-4">Recent Registrations</h4>
    <div class="card p-4">
        <!-- Add a simple table or list of recent businesses here later if needed -->
        <p class="text-muted mb-0">No recent activity to show.</p>
    </div>
</div>
@endsection
