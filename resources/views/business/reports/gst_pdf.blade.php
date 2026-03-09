<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GST Report ({{ $startDate }} to {{ $endDate }})</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .business-name { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .report-title { font-size: 16px; margin-top: 5px; font-weight: bold; }
        .summary-box { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-box td { padding: 10px; border: 1px solid #e2e8f0; width: 25%; text-align: center; }
        .summary-label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .summary-value { font-size: 14px; font-weight: bold; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f1f5f9; padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-name">{{ auth()->user()->business->business_name }}</div>
        <div class="report-title">GST REPORT</div>
        <div style="color: #64748b;">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
        @if(auth()->user()->business->gst_number)
            <div style="margin-top: 5px;"><strong>GSTIN:</strong> {{ auth()->user()->business->gst_number }}</div>
        @endif
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <div class="summary-label">Total Sales</div>
                <div class="summary-value">₹{{ number_format($summary['total_sales'], 2) }}</div>
            </td>
            <td>
                <div class="summary-label">Total CGST</div>
                <div class="summary-value" style="color: #0891b2;">₹{{ number_format($summary['total_cgst'], 2) }}</div>
            </td>
            <td>
                <div class="summary-label">Total SGST</div>
                <div class="summary-value" style="color: #d97706;">₹{{ number_format($summary['total_sgst'], 2) }}</div>
            </td>
            <td>
                <div class="summary-label">Total IGST</div>
                <div class="summary-value" style="color: #dc2626;">₹{{ number_format($summary['total_igst'], 2) }}</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Total Tax</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td style="font-size: 9px;">{{ $invoice->created_at->format('d M y') }}</td>
                <td style="font-weight: bold;">{{ $invoice->invoice_number }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $invoice->customer->name }}</div>
                    @if($invoice->customer->gst_number)
                        <div style="font-size: 8px; color: #64748b;">GST: {{ $invoice->customer->gst_number }}</div>
                    @endif
                </td>
                <td style="color: #0891b2;">{{ number_format($invoice->cgst_amount, 2) }}</td>
                <td style="color: #d97706;">{{ number_format($invoice->sgst_amount, 2) }}</td>
                <td style="color: #dc2626;">{{ number_format($invoice->igst_amount, 2) }}</td>
                <td style="font-weight: bold;">{{ number_format($invoice->tax_amount, 2) }}</td>
                <td class="text-right" style="font-weight: bold; color: #4f46e5;">₹{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ date('d M Y, h:i A') }} | This is a computer-generated report.
    </div>
</body>
</html>
