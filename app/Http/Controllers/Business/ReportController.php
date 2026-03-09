<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    public function index()
    {
        $totalSales = Invoice::sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalSales - $totalExpenses;
        
        $salesByMonth = Invoice::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )->groupBy('month')->orderBy('created_at')->get();

        $topProducts = DB::connection('tenant')->table('invoice_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('product_name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        return view('business.reports.index', compact('totalSales', 'totalExpenses', 'netProfit', 'salesByMonth', 'topProducts'));
    }

    public function export()
    {
        $data = Invoice::select('invoice_number', 'total_amount', 'created_at', 'status')
            ->with('customer:id,name')
            ->get()
            ->map(function($invoice) {
                return [
                    'Invoice #' => $invoice->invoice_number,
                    'Customer' => $invoice->customer->name ?? 'N/A',
                    'Date' => $invoice->created_at->format('d M Y'),
                    'Amount' => $invoice->total_amount,
                    'Status' => ucfirst($invoice->status),
                ];
            });

        return Excel::download(
            new ReportsExport($data, 'Sales Report', ['Invoice #', 'Customer', 'Date', 'Amount', 'Status']), 
            'sales_report_' . date('Y-m-d') . '.xlsx'
        );
    }
}
