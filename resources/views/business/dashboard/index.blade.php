@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Business Dashboard</h2>
    <div class="text-muted">{{ now()->format('l, d M Y') }}</div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-file-invoice text-primary fs-4"></i>
                </div>
            </div>
            <h6 class="text-muted mb-1">Total Invoices</h6>
            <h3 class="fw-bold mb-0">{{ $stats['total_invoices'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-users text-success fs-4"></i>
                </div>
            </div>
            <h6 class="text-muted mb-1">Customers</h6>
            <h3 class="fw-bold mb-0">{{ $stats['total_customers'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                </div>
            </div>
            <h6 class="text-muted mb-1">Low Stock</h6>
            <h3 class="fw-bold mb-0 {{ $stats['low_stock_count'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock_count'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="bg-info bg-opacity-10 p-3 rounded-3">
                    <i class="fas fa-wallet text-info fs-4"></i>
                </div>
                <div class="text-muted small">This Month</div>
            </div>
            <h6 class="text-muted mb-1">Monthly Expenses</h6>
            <h3 class="fw-bold mb-0 text-info">₹{{ number_format($stats['monthly_expenses'], 2) }}</h3>
        </div>
    </div>
</div>

<div class="row mt-4 g-4">
    <div class="col-md-8">
        <div class="card p-4 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Sales vs Expenses (Last 6 Months)</h5>
            </div>
            <div style="height: 350px;">
                <canvas id="businessAnalyticsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-4">Sales Distribution</h5>
            <div style="height: 300px; position: relative;">
                <canvas id="salesDistributionChart"></canvas>
            </div>
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted"><i class="fas fa-circle text-primary me-2"></i>B2B (Business)</span>
                    <span class="small fw-bold">₹{{ number_format($distributionData['data'][0], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-muted"><i class="fas fa-circle text-success me-2"></i>B2C (Individual)</span>
                    <span class="small fw-bold">₹{{ number_format($distributionData['data'][1], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-4">
    <div class="col-md-8">
        <div class="card p-4 border-0 shadow-sm">
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
                        @forelse(\App\Models\Invoice::latest()->with('customer')->take(5)->get() as $invoice)
                        <tr>
                            <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td class="fw-bold">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-filter d-block mb-2 fs-3 opacity-25"></i>
                                No invoices found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-4">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('business.invoices.create') }}" class="btn btn-primary text-start p-3 border-0 transition-all hover-lift">
                    <i class="fas fa-plus-circle me-3"></i> Create New Invoice
                </a>
                <a href="{{ route('business.customers.index') }}" class="btn btn-outline-primary text-start p-3 transition-all hover-lift">
                    <i class="fas fa-user-plus me-3"></i> Add Customer
                </a>
                <a href="{{ route('business.products.index') }}" class="btn btn-outline-secondary text-start p-3 transition-all hover-lift">
                    <i class="fas fa-box-open me-3"></i> Manage Inventory
                </a>
                <a href="{{ route('business.reports.index') }}" class="btn btn-outline-dark text-start p-3 transition-all hover-lift">
                    <i class="fas fa-chart-line me-3"></i> View Detailed Reports
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('businessAnalyticsChart').getContext('2d');
        
        // Creating gradients for a more premium look
        const salesGradient = ctx.createLinearGradient(0, 0, 0, 400);
        salesGradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        salesGradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        const expenseGradient = ctx.createLinearGradient(0, 0, 0, 400);
        expenseGradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        expenseGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Gross Sales',
                        data: {!! json_encode($chartData['sales']) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: salesGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($chartData['expenses']) !!},
                        borderColor: '#10b981',
                        backgroundColor: expenseGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            padding: 10,
                            callback: function(value) {
                                if (value >= 1000) {
                                    return '₹' + (value / 1000) + 'k';
                                }
                                return '₹' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });

        // Sales Distribution Doughnut Chart
        const distCtx = document.getElementById('salesDistributionChart').getContext('2d');
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($distributionData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($distributionData['data']) !!},
                    backgroundColor: ['#4f46e5', '#10b981'],
                    hoverOffset: 4,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
