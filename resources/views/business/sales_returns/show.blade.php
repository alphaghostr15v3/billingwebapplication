@extends('layouts.business')

@section('content')
<div class="mb-4">
    <h2>Credit Note Details</h2>
    <p class="text-muted">{{ $salesReturn->return_number }}</p>
</div>

<div class="card p-4">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h5 class="mb-3">To:</h5>
            <div><strong>{{ $salesReturn->customer->name }}</strong></div>
            <div>{{ $salesReturn->customer->email }}</div>
            <div>{{ $salesReturn->customer->phone }}</div>
            <div>{{ $salesReturn->customer->address }}</div>
        </div>
        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
            <h5 class="mb-3">Credit Note Info:</h5>
            <div><strong>Note #:</strong> {{ $salesReturn->return_number }}</div>
            <div><strong>Date:</strong> {{ $salesReturn->date->format('d M Y') }}</div>
            @if($salesReturn->invoice)
            <div><strong>Original Invoice #:</strong> {{ $salesReturn->invoice->invoice_number }}</div>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesReturn->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>₹{{ number_format($item->quantity * $item->price, 2) }}</td>
                    <td>₹{{ number_format($item->tax_amount, 2) }} @if($item->tax_rate) ({{ $item->tax_rate }}%) @endif</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row mt-4">
        <div class="col-sm-6">
            @if($salesReturn->reason)
            <div class="mb-3">
                <strong>Reason for Return:</strong>
                <p class="text-muted">{{ $salesReturn->reason }}</p>
            </div>
            @endif
        </div>
        <div class="col-sm-6 text-end">
            <div class="mb-2">Subtotal: ₹{{ number_format($salesReturn->subtotal, 2) }}</div>
            <div class="mb-2">Tax: ₹{{ number_format($salesReturn->tax_amount, 2) }}</div>
            <div class="fs-4 fw-bold mt-3">Total: ₹{{ number_format($salesReturn->total_amount, 2) }}</div>
        </div>
    </div>

    <div class="mt-5 text-end border-top pt-4">
        <a href="{{ route('business.sales-returns.index') }}" class="btn btn-light me-2">Back to List</a>
        <a href="{{ route('business.sales-returns.download', $salesReturn->id) }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
    </div>
</div>
@endsection
