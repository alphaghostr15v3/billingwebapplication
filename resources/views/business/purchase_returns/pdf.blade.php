<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Debit Note {{ $purchaseReturn->return_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', sans-serif; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .company-details { font-size: 14px; }
        .note-details { text-align: right; }
        .title { font-size: 28px; color: #e54646; margin-bottom: 5px; }
        .billing-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .totals { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; text-align: right; }
        .totals-row { margin-bottom: 10px; }
        .totals-row strong { margin-left: 20px; }
        .total-amount { font-size: 20px; font-weight: bold; color: #e54646; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-details">
            <h2>DEBIT NOTE</h2>
            <strong>{{ auth()->user()->business->business_name ?? 'Business Name' }}</strong><br>
            {{ auth()->user()->business->address ?? '' }}<br>
            {{ auth()->user()->business->email ?? '' }}
        </div>
        <div class="note-details">
            <div class="title">{{ $purchaseReturn->return_number }}</div>
            <div>Date: {{ $purchaseReturn->date->format('d M Y') }}</div>
            @if($purchaseReturn->expense)
            <div>Expense Ref: {{ $purchaseReturn->expense->title }}</div>
            @endif
        </div>
    </div>

    <div class="billing-info">
        <div>
            <strong>Debit To (Vendor):</strong><br>
            {{ $purchaseReturn->vendor_name }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Tax</th>
                <th>Total Refund</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseReturn->items as $item)
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
        <div class="totals-row">Subtotal: <strong>{{ number_format($purchaseReturn->subtotal, 2) }}</strong></div>
        <div class="totals-row">Tax Amount: <strong>{{ number_format($purchaseReturn->tax_amount, 2) }}</strong></div>
        <div class="total-amount">Total Refund: {{ number_format($purchaseReturn->total_amount, 2) }}</div>
    </div>
    
    @if($purchaseReturn->reason)
    <div style="margin-top: 50px;">
        <strong>Reason for Return:</strong>
        <p>{{ $purchaseReturn->reason }}</p>
    </div>
    @endif
</body>
</html>
