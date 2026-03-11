@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>GSTR-1 Report</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('business.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>
</div>

<div class="card p-4 mb-4 border-0 shadow-sm">
    <form action="{{ route('business.reports.gstr1') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted text-uppercase">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted text-uppercase">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter me-2"></i>Filter Report
            </button>
        </div>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">B2B Invoices</h5>
                        <div class="text-muted small">Registered Business Sales</div>
                    </div>
                    <a href="{{ route('business.reports.gstr1.b2b', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Export B2B
                    </a>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Count</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['b2b_count'] }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Taxable Value</div>
                        <div class="h4 fw-bold mb-0">₹{{ number_format($summary['b2b_taxable'], 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Total Tax</div>
                        <div class="h4 fw-bold mb-0">₹{{ number_format($summary['b2b_tax'], 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Total Value</div>
                        <div class="h4 fw-bold mb-0 text-primary">₹{{ number_format($summary['b2b_total'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">B2C Invoices</h5>
                        <div class="text-muted small">Consumer Sales (Unregistered)</div>
                    </div>
                    <a href="{{ route('business.reports.gstr1.b2c', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-info btn-sm text-white">
                        <i class="fas fa-file-excel me-2"></i>Export B2C
                    </a>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Count</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['b2c_count'] }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Taxable Value</div>
                        <div class="h4 fw-bold mb-0">₹{{ number_format($summary['b2c_taxable'], 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Total Tax</div>
                        <div class="h4 fw-bold mb-0">₹{{ number_format($summary['b2c_tax'], 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted text-uppercase fw-bold">Total Value</div>
                        <div class="h4 fw-bold mb-0 text-info">₹{{ number_format($summary['b2c_total'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-0">
        <h5 class="fw-bold mb-0">Detailed GSTR-1 Breakdown</h5>
    </div>
    <div class="card-body px-4 pb-4 pt-0">
        <ul class="nav nav-tabs mb-4" id="gstrTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" id="b2b-tab" data-bs-toggle="tab" data-bs-target="#b2b" type="button">B2B Sales</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="b2c-tab" data-bs-toggle="tab" data-bs-target="#b2c" type="button">B2C Sales</button>
            </li>
        </ul>
        <div class="tab-content" id="gstrTabContent">
            <div class="tab-pane fade show active" id="b2b">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable">
                        <thead class="table-light">
                            <tr>
                                <th>GSTIN</th>
                                <th>Receiver</th>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Taxable Value</th>
                                <th>Total Tax</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b2bInvoices as $invoice)
                            <tr>
                                <td><code class="bg-light px-2 py-1 rounded text-dark">{{ $invoice->customer->gst_number }}</code></td>
                                <td class="fw-bold">{{ $invoice->customer->name }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
                                <td>₹{{ number_format($invoice->tax_amount, 2) }}</td>
                                <td class="fw-bold text-primary">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="b2c">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Taxable Value</th>
                                <th>Total Tax</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b2cInvoices as $invoice)
                            <tr>
                                <td class="fw-bold">{{ $invoice->customer->name }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
                                <td>₹{{ number_format($invoice->tax_amount, 2) }}</td>
                                <td class="fw-bold text-info">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            order: [[3, 'desc']]
        });
    });
</script>
@endsection
