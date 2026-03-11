<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\DB;

class GSTR1Controller extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Invoice::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])->with('customer');

        $invoices = $query->latest()->get();

        $b2bInvoices = $invoices->filter(fn($inv) => !empty($inv->customer->gst_number));
        $b2cInvoices = $invoices->filter(fn($inv) => empty($inv->customer->gst_number));

        $summary = [
            'b2b_count' => $b2bInvoices->count(),
            'b2b_taxable' => $b2bInvoices->sum('subtotal'),
            'b2b_tax' => $b2bInvoices->sum('tax_amount'),
            'b2b_total' => $b2bInvoices->sum('total_amount'),
            
            'b2c_count' => $b2cInvoices->count(),
            'b2c_taxable' => $b2cInvoices->sum('subtotal'),
            'b2c_tax' => $b2cInvoices->sum('tax_amount'),
            'b2c_total' => $b2cInvoices->sum('total_amount'),
        ];

        return view('business.reports.gstr1', compact('b2bInvoices', 'b2cInvoices', 'summary', 'startDate', 'endDate'));
    }

    public function exportB2B(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = Invoice::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->whereHas('customer', function($q) {
                $q->whereNotNull('gst_number')->where('gst_number', '!=', '');
            })
            ->with(['customer', 'items'])
            ->get()
            ->flatMap(function($invoice) {
                // GSTR-1 B2B requires breakdown by rate
                return $invoice->items->groupBy('gst_percentage')->map(function($items, $rate) use ($invoice) {
                    $taxableValue = $items->sum('total') / (1 + ($rate / 100)); // Assuming total includes tax
                    // Wait, let me check InvoiceItem total calculation
                    // Looking at InvoiceController.php again... 
                    // 'total' => $item['total'] was passed from JS.
                    // Let's re-verify the logic.
                    return [
                        'GSTIN/UIN of Recipient' => $invoice->customer->gst_number,
                        'Receiver Name' => $invoice->customer->name,
                        'Invoice Number' => $invoice->invoice_number,
                        'Invoice Date' => $invoice->created_at->format('d-M-Y'),
                        'Invoice Value' => $invoice->total_amount,
                        'Place Of Supply' => $invoice->customer->state ?? 'N/A',
                        'Reverse Charge' => 'N',
                        'Applicable % of Tax Rate' => '',
                        'Invoice Type' => 'Regular',
                        'E-Commerce GSTIN' => '',
                        'Rate' => $rate,
                        'Taxable Value' => number_format($items->sum(function($i) { return $i->price * $i->quantity; }), 2, '.', ''),
                        'Cess Amount' => 0,
                    ];
                });
            })->values();

        $headings = [
            'GSTIN/UIN of Recipient', 'Receiver Name', 'Invoice Number', 'Invoice Date', 'Invoice Value', 
            'Place Of Supply', 'Reverse Charge', 'Applicable % of Tax Rate', 'Invoice Type', 
            'E-Commerce GSTIN', 'Rate', 'Taxable Value', 'Cess Amount'
        ];

        return Excel::download(
            new ReportsExport($data, 'GSTR-1 B2B', $headings), 
            'GSTR1_B2B_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    public function exportB2C(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = Invoice::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->whereHas('customer', function($q) {
                $q->whereNull('gst_number')->orWhere('gst_number', '');
            })
            ->with(['customer', 'items'])
            ->get()
            ->flatMap(function($invoice) {
                return $invoice->items->groupBy('gst_percentage')->map(function($items, $rate) use ($invoice) {
                    return [
                        'Type' => 'OE', // Other than E-commerce
                        'Place Of Supply' => $invoice->customer->state ?? 'N/A',
                        'Applicable % of Tax Rate' => '',
                        'Rate' => $rate,
                        'Taxable Value' => number_format($items->sum(function($i) { return $i->price * $i->quantity; }), 2, '.', ''),
                        'Cess Amount' => 0,
                        'E-Commerce GSTIN' => '',
                    ];
                });
            })->values();

        $headings = [
            'Type', 'Place Of Supply', 'Applicable % of Tax Rate', 'Rate', 'Taxable Value', 'Cess Amount', 'E-Commerce GSTIN'
        ];

        return Excel::download(
            new ReportsExport($data, 'GSTR-1 B2CS', $headings), 
            'GSTR1_B2CS_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }
}
