@extends('layouts.business')

@section('content')
<div class="mb-4 d-print-none">
    <a href="{{ route('business.invoices.index') }}" class="text-decoration-none text-muted">
        <i class="fas fa-arrow-left me-2"></i>Back to Invoices
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <h2 class="fw-bold">Invoice Details</h2>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Invoice
            </button>
        </div>
    </div>
</div>

<div class="card p-5 shadow-sm border-0 bg-white" id="invoice-printable">
    <div class="row mb-5">
        <div class="col-6">
            <h1 class="fw-bold text-primary mb-1">INVOICE</h1>
            <div class="text-muted">#{{ $invoice->invoice_number }}</div>
            <div class="mt-4">
                <div class="fw-bold text-dark">Invoice Date:</div>
                <div>{{ $invoice->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        <div class="col-6 text-end">
            <h4 class="fw-bold mb-1">{{ auth()->user()->business->business_name }}</h4>
            <div class="text-muted small">
                {{ auth()->user()->business->owner_name }}<br>
                {{ auth()->user()->business->phone }}<br>
                {{ auth()->user()->business->email }}
                @if(auth()->user()->business->gst_number)
                    <br><span class="fw-bold">GSTIN:</span> {{ auth()->user()->business->gst_number }}
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-5 bg-light p-4 rounded-4 mx-0">
        <div class="col-6 border-end">
            <h6 class="text-muted text-uppercase small mb-3">Billed To:</h6>
            <h5 class="fw-bold mb-1">{{ $invoice->customer->name }}</h5>
            <div class="text-muted small">
                Phone: {{ $invoice->customer->phone }}<br>
                Email: {{ $invoice->customer->email ?? 'N/A' }}<br>
                Address: {{ $invoice->customer->address ?? 'N/A' }}
                @if($invoice->customer->gst_number)
                    <br><span class="fw-bold">GSTIN:</span> {{ $invoice->customer->gst_number }}
                @endif
            </div>
        </div>
        <div class="col-6 ps-4 text-end">
            <h6 class="text-muted text-uppercase small mb-3">Payment Info:</h6>
            <div class="mb-1">Method: <span class="fw-semibold">{{ strtoupper($invoice->payment_method) }}</span></div>
            <div>Status: <span class="badge bg-success">PAID</span></div>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table class="table table-borderless">
            <thead class="bg-light">
                <tr class="text-uppercase small">
                    <th class="ps-3 py-3">Description</th>
                    <th class="text-center py-3">HSN</th>
                    <th class="text-center py-3">Price</th>
                    <th class="text-center py-3">Qty</th>
                    <th class="text-end pe-3 py-3">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-bottom">
                    <td class="ps-3 py-4">
                        <div class="fw-bold">{{ $item->product_name }}</div>
                        <div class="text-muted small">Tax ({{ $item->gst_percentage }}%)</div>
                    </td>
                    <td class="text-center py-4">{{ $item->hsn_number ?? '-' }}</td>
                    <td class="text-center py-4">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-center py-4">{{ $item->quantity }}</td>
                    <td class="text-end pe-3 py-4 fw-bold">₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Subtotal:</span>
                <span class="fw-bold">₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">GST Total:</span>
                <span class="fw-bold">₹{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            @if($invoice->cgst_amount > 0)
            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>CGST:</span>
                <span>₹{{ number_format($invoice->cgst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->sgst_amount > 0)
            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>SGST:</span>
                <span>₹{{ number_format($invoice->sgst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->igst_amount > 0)
            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>IGST:</span>
                <span>₹{{ number_format($invoice->igst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->discount_amount > 0)
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Discount:</span>
                <span class="fw-bold text-danger">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
            @endif
            <hr>
            <div class="d-flex justify-content-between">
                <span class="h4 fw-bold">Grand Total:</span>
                <span class="h4 fw-bold text-primary">₹{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="mt-5 pt-5 text-center text-muted small">
        Thank you for your business! This is a computer-generated invoice.
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .card { box-shadow: none !important; margin: 0 !important; padding: 0 !important; border: none !important; }
    #wrapper { display: block !important; }
    #sidebar-wrapper, .navbar, .d-print-none { display: none !important; }
    #page-content-wrapper { padding: 0 !important; margin: 0 !important; }
    .content-area { padding: 0 !important; }
}
</style>
<script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print')) {
            window.print();
        }
    }
</script>
@endsection
