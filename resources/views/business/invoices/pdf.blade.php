<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.5; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; }
        .invoice-title { font-size: 32px; font-weight: bold; color: #4f46e5; margin-bottom: 5px; }
        .header-table { width: 100%; margin-bottom: 40px; }
        .billed-table { width: 100%; margin-bottom: 40px; background: #f8fafc; padding: 20px; border-radius: 10px; }
        table { width: 100%; text-align: left; border-collapse: collapse; }
        th { background: #f1f5f9; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .total-section { margin-top: 30px; float: right; width: 300px; }
        .total-row { display: table; width: 100%; margin-bottom: 5px; }
        .total-label { display: table-cell; text-align: left; color: #64748b; }
        .total-value { display: table-cell; text-align: right; font-weight: bold; }
        .grand-total { font-size: 20px; font-weight: bold; color: #4f46e5; border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 10px; }
        .footer { margin-top: 100px; text-align: center; font-size: 12px; color: #64748b; clear: both; }
        .currency { font-family: 'DejaVu Sans'; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <div class="invoice-title">INVOICE</div>
                    <div style="color: #64748b;">#{{ $invoice->invoice_number }}</div>
                    <div style="margin-top: 10px;">Date: {{ $invoice->created_at->format('d M Y') }}</div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 20px; font-weight: bold;">{{ auth()->user()->business->business_name }}</div>
                    <div style="color: #64748b; font-size: 13px;">
                        {{ auth()->user()->business->phone }}<br>
                        {{ auth()->user()->business->email }}
                        @if(auth()->user()->business->gst_number)
                            <br><strong>GSTIN:</strong> {{ auth()->user()->business->gst_number }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="billed-table">
            <tr>
                <td style="border: none; width: 60%;">
                    <div style="color: #64748b; text-transform: uppercase; font-size: 11px; font-weight: bold; margin-bottom: 5px;">Billed To:</div>
                    <div style="font-size: 16px; font-weight: bold;">{{ $invoice->customer->name }}</div>
                    <div style="color: #64748b;">
                        {{ $invoice->customer->phone }}<br>
                        {{ $invoice->customer->address }}
                    </div>
                </td>
                <td style="border: none; text-align: right;">
                    <div style="color: #64748b; text-transform: uppercase; font-size: 11px; font-weight: bold; margin-bottom: 5px;">Payment Status:</div>
                    <div style="font-size: 18px; font-weight: bold; color: #10b981;">PAID</div>
                    <div style="color: #64748b; margin-top: 5px;">Method: {{ strtoupper($invoice->payment_method) }}</div>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">HSN</th>
                    <th style="text-align: center;">Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $item->product_name }}</div>
                        <div style="color: #64748b; font-size: 11px;">Tax: {{ $item->gst_percentage }}%</div>
                    </td>
                    <td style="text-align: center;">{{ $item->hsn_number ?? '-' }}</td>
                    <td style="text-align: center;">&#8377;{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right; font-weight: bold;">&#8377;{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">&#8377;{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">GST Total:</span>
                <span class="total-value">&#8377;{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            @if($invoice->cgst_amount > 0)
            <div class="total-row" style="font-size: 11px; color: #64748b;">
                <span class="total-label">CGST:</span>
                <span class="total-value">&#8377;{{ number_format($invoice->cgst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->sgst_amount > 0)
            <div class="total-row" style="font-size: 11px; color: #64748b;">
                <span class="total-label">SGST:</span>
                <span class="total-value">&#8377;{{ number_format($invoice->sgst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->igst_amount > 0)
            <div class="total-row" style="font-size: 11px; color: #64748b;">
                <span class="total-label">IGST:</span>
                <span class="total-value">&#8377;{{ number_format($invoice->igst_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->discount_amount > 0)
            <div class="total-row">
                <span class="total-label">Discount:</span>
                <span class="total-value" style="color: #ef4444;">-&#8377;{{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="grand-total">
                <span style="float: left;">Grand Total:</span>
                <span style="float: right;">&#8377;{{ number_format($invoice->total_amount, 2) }}</span>
                <div style="clear: both;"></div>
            </div>
        </div>

        <div class="footer">
            Thank you for your business! This is a computer-generated invoice.
        </div>
    </div>
</body>
</html>
