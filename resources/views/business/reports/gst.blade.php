@extends('layouts.business')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>GST Reports</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('business.reports.gst.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
        <a href="{{ route('business.reports.gst.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Export to Excel
        </a>
    </div>
</div>

<div class="card p-4 mb-4 border-0 shadow-sm">
    <form action="{{ route('business.reports.gst') }}" method="GET" class="row g-3 align-items-end">
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
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm h-100 bg-primary text-white">
            <div class="small text-uppercase fw-bold opacity-75">Total Sales</div>
            <div class="h3 fw-bold mt-2">₹{{ number_format($summary['total_sales'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm h-100 bg-info text-white">
            <div class="small text-uppercase fw-bold opacity-75">Total CGST</div>
            <div class="h3 fw-bold mt-2">₹{{ number_format($summary['total_cgst'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm h-100 bg-warning text-white">
            <div class="small text-uppercase fw-bold opacity-75">Total SGST</div>
            <div class="h3 fw-bold mt-2">₹{{ number_format($summary['total_sgst'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm h-100 bg-danger text-white">
            <div class="small text-uppercase fw-bold opacity-75">Total IGST</div>
            <div class="h3 fw-bold mt-2">₹{{ number_format($summary['total_igst'], 2) }}</div>
        </div>
    </div>
</div>

<div class="card p-4 border-0 shadow-sm">
    <h5 class="fw-bold mb-4">GST Invoice Breakdown</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="gstTable">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Subtotal</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Total Tax</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->created_at->format('d M Y') }}</td>
                    <td><span class="fw-semibold">{{ $invoice->invoice_number }}</span></td>
                    <td>
                        <div class="fw-bold">{{ $invoice->customer->name }}</div>
                        <small class="text-muted">GST: {{ $invoice->customer->gst_number ?? 'N/A' }}</small>
                    </td>
                    <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-info">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                    <td class="text-warning">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                    <td class="text-danger">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                    <td class="fw-bold">₹{{ number_format($invoice->tax_amount, 2) }}</td>
                    <td class="fw-bold text-primary">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#gstTable').DataTable({
            order: [[0, 'desc']]
        });
    });
</script>
@endsection
