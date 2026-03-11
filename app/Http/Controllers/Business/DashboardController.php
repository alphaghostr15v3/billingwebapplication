<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_customers' => Customer::count(),
            'low_stock_count' => Product::whereRaw('stock_quantity <= low_stock_limit')->count(),
            'monthly_expenses' => Expense::whereMonth('date', Carbon::now()->month)
                                        ->whereYear('date', Carbon::now()->year)
                                        ->sum('amount'),
        ];

        // Chart Data for last 6 months
        $months = [];
        $salesData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $salesData[] = Invoice::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');

            $expenseData[] = Expense::whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount');
        }

        $chartData = [
            'labels' => $months,
            'sales' => $salesData,
            'expenses' => $expenseData,
        ];

        // Sales Distribution (B2B vs B2C)
        $b2bSales = Invoice::whereHas('customer', fn($q) => $q->where('customer_type', 'business'))->sum('total_amount');
        $b2cSales = Invoice::whereHas('customer', fn($q) => $q->where('customer_type', 'individual'))->sum('total_amount');

        $distributionData = [
            'labels' => ['B2B Sales', 'B2C Sales'],
            'data' => [$b2bSales, $b2cSales],
        ];

        return view('business.dashboard.index', compact('stats', 'chartData', 'distributionData'));
    }
}
