@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Business Dashboard</h2>
    <div class="text-muted">{{ now()->format('l, d M Y') }}</div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-file-invoice text-primary fs-4"></i>
                </div>
                <div class="text-success small fw-bold">+12%</div>
            </div>
            <h6 class="text-muted mb-1">Total Invoices</h6>
            <h3 class="fw-bold mb-0">{{ \App\Models\Invoice::count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-users text-success fs-4"></i>
                </div>
                <div class="text-success small fw-bold">+5%</div>
            </div>
            <h6 class="text-muted mb-1">Customers</h6>
            <h3 class="fw-bold mb-0">{{ \App\Models\Customer::count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-info bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-box text-info fs-4"></i>
                </div>
                <div class="text-danger small fw-bold">-2</div>
            </div>
            <h6 class="text-muted mb-1">Low Stock</h6>
            <h3 class="fw-bold mb-0">0</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-wallet text-warning fs-4"></i>
                </div>
                <div class="text-muted small">This Month</div>
            </div>
            <h6 class="text-muted mb-1">Expenses</h6>
            <h3 class="fw-bold mb-0">₹0</h3>
        </div>
    </div>
</div>

<div class="row mt-5 g-4">
    <div class="col-md-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Recent Invoices</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Inv #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Invoice::latest()->take(5)->get() as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">No invoices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('business.invoices.create') }}" class="btn btn-primary text-start p-3">
                    <i class="fas fa-plus-circle me-3"></i> Create New Invoice
                </a>
                <a href="{{ route('business.customers.index') }}" class="btn btn-outline-primary text-start p-3">
                    <i class="fas fa-user-plus me-3"></i> Add Customer
                </a>
                <a href="{{ route('business.products.index') }}" class="btn btn-outline-secondary text-start p-3">
                    <i class="fas fa-box-open me-3"></i> Manage Inventory
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
