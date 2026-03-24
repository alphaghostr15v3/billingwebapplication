@extends('layouts.business')

@section('content')
<div class="mb-4">
    <h2>Debit Note Details</h2>
    <p class="text-muted">{{ $purchaseReturn->return_number }}</p>
</div>

<div class="card p-4">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h5 class="mb-3">To Vendor/Supplier:</h5>
            <div><strong>{{ $purchaseReturn->vendor_name }}</strong></div>
        </div>
        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
            <h5 class="mb-3">Debit Note Info:</h5>
            <div><strong>Note #:</strong> {{ $purchaseReturn->return_number }}</div>
            <div><strong>Date:</strong> {{ $purchaseReturn->date->format('d M Y') }}</div>
            @if($purchaseReturn->expense)
            <div><strong>Expense Ref:</strong> {{ $purchaseReturn->expense->title }}</div>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item/Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Total Refund</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseReturn->items as $item)
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
            @if($purchaseReturn->reason)
            <div class="mb-3">
                <strong>Reason for Return:</strong>
                <p class="text-muted">{{ $purchaseReturn->reason }}</p>
            </div>
            @endif
        </div>
        <div class="col-sm-6 text-end">
            <div class="mb-2">Subtotal: ₹{{ number_format($purchaseReturn->subtotal, 2) }}</div>
            <div class="mb-2">Tax: ₹{{ number_format($purchaseReturn->tax_amount, 2) }}</div>
            <div class="fs-4 fw-bold mt-3">Total Refund: ₹{{ number_format($purchaseReturn->total_amount, 2) }}</div>
        </div>
    </div>

    <div class="mt-5 text-end border-top pt-4">
        <a href="{{ route('business.purchase-returns.index') }}" class="btn btn-light me-2">Back to List</a>
        <a href="{{ route('business.purchase-returns.download', $purchaseReturn->id) }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
    </div>
</div>
@endsection
