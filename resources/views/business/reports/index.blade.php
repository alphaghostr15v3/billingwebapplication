@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Business Reports</h2>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary d-print-none">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
        <a href="{{ route('business.reports.export') }}" class="btn btn-primary d-print-none">
            <i class="fas fa-download me-2"></i>Export Excel
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card p-4 border-start border-4 border-primary">
            <h6 class="text-muted mb-2">Total Sales (Lifetime)</h6>
            <h2 class="fw-bold mb-0 text-primary">₹{{ number_format($totalSales, 2) }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-start border-4 border-danger">
            <h6 class="text-muted mb-2">Total Expenses (Lifetime)</h6>
            <h2 class="fw-bold mb-0 text-danger">₹{{ number_format($totalExpenses, 2) }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-start border-4 border-success">
            <h6 class="text-muted mb-2">Net Profit</h6>
            <h2 class="fw-bold mb-0 text-success">₹{{ number_format($netProfit, 2) }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card p-4">
            <h5 class="fw-bold mb-4 text-dark">Top Selling Products</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td class="text-center">{{ $product->total_qty }}</td>
                            <td class="text-end fw-bold text-primary">₹{{ number_format($product->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No sales data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card p-4">
            <h5 class="fw-bold mb-4 text-dark">Sales Summary</h5>
            @foreach($salesByMonth as $sale)
            <div class="d-flex align-items-center mb-3">
                <div class="flex-grow-1">
                    <div class="fw-semibold mb-1">{{ $sale->month }}</div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="ms-3 fw-bold">₹{{ number_format($sale->total, 2) }}</div>
            </div>
            @endforeach
            @if($salesByMonth->isEmpty())
                <p class="text-center py-4 text-muted">No monthly sales data available.</p>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    #sidebar-wrapper, .navbar, .d-print-none { display: none !important; }
    #page-content-wrapper { padding: 0 !important; margin: 0 !important; }
    .content-area { padding: 0 !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>
@endsection
