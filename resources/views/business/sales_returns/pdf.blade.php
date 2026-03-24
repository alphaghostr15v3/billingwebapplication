<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Note {{ $salesReturn->return_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', sans-serif; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .company-details { font-size: 14px; }
        .note-details { text-align: right; }
        .title { font-size: 28px; color: #4f46e5; margin-bottom: 5px; }
        .billing-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .totals { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; text-align: right; }
        .totals-row { margin-bottom: 10px; }
        .totals-row strong { margin-left: 20px; }
        .total-amount { font-size: 20px; font-weight: bold; color: #4f46e5; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-details">
            <h2>CREDIT NOTE</h2>
            <strong>{{ auth()->user()->business->business_name ?? 'Business Name' }}</strong><br>
            {{ auth()->user()->business->address ?? '' }}<br>
            {{ auth()->user()->business->email ?? '' }}
        </div>
        <div class="note-details">
            <div class="title">{{ $salesReturn->return_number }}</div>
            <div>Date: {{ $salesReturn->date->format('d M Y') }}</div>
            @if($salesReturn->invoice)
            <div>Invoice Ref: {{ $salesReturn->invoice->invoice_number }}</div>
            @endif
        </div>
    </div>

    <div class="billing-info">
        <div>
            <strong>Credit To:</strong><br>
            {{ $salesReturn->customer->name }}<br>
            {{ $salesReturn->customer->address }}<br>
            {{ $salesReturn->customer->phone }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesReturn->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($item->tax_amount, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">Subtotal: <strong>{{ number_format($salesReturn->subtotal, 2) }}</strong></div>
        <div class="totals-row">Tax Amount: <strong>{{ number_format($salesReturn->tax_amount, 2) }}</strong></div>
        <div class="total-amount">Total: {{ number_format($salesReturn->total_amount, 2) }}</div>
    </div>
    
    @if($salesReturn->reason)
    <div style="margin-top: 50px;">
        <strong>Reason for Return:</strong>
        <p>{{ $salesReturn->reason }}</p>
    </div>
    @endif
</body>
</html>
