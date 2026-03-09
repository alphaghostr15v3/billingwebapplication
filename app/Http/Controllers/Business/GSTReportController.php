<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Carbon\Carbon;

class GSTReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Invoice::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        $invoices = (clone $query)->with('customer')->latest()->get();

        $summary = [
            'total_sales' => (clone $query)->sum('total_amount'),
            'total_tax' => (clone $query)->sum('tax_amount'),
            'total_cgst' => (clone $query)->sum('cgst_amount'),
            'total_sgst' => (clone $query)->sum('sgst_amount'),
            'total_igst' => (clone $query)->sum('igst_amount'),
        ];

        return view('business.reports.gst', compact('invoices', 'summary', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = Invoice::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->with('customer:id,name,gst_number')
            ->get()
            ->map(function($invoice) {
                return [
                    'Date' => $invoice->created_at->format('d M Y'),
                    'Invoice #' => $invoice->invoice_number,
                    'Customer' => $invoice->customer->name ?? 'N/A',
                    'Customer GST' => $invoice->customer->gst_number ?? 'N/A',
                    'Subtotal' => $invoice->subtotal,
                    'CGST' => $invoice->cgst_amount,
                    'SGST' => $invoice->sgst_amount,
                    'IGST' => $invoice->igst_amount,
                    'Total Tax' => $invoice->tax_amount,
                    'Total Amount' => $invoice->total_amount,
                ];
            });

        $headings = ['Date', 'Invoice #', 'Customer', 'Customer GST', 'Subtotal', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Total Amount'];

        return Excel::download(
            new ReportsExport($data, 'GST Report', $headings), 
            'gst_report_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    public function downloadPDF(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Invoice::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        $invoices = (clone $query)->with('customer')->latest()->get();

        $summary = [
            'total_sales' => (clone $query)->sum('total_amount'),
            'total_tax' => (clone $query)->sum('tax_amount'),
            'total_cgst' => (clone $query)->sum('cgst_amount'),
            'total_sgst' => (clone $query)->sum('sgst_amount'),
            'total_igst' => (clone $query)->sum('igst_amount'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('business.reports.gst_pdf', compact('invoices', 'summary', 'startDate', 'endDate'));
        return $pdf->download('gst_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }
}
