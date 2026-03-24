<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesReturnController extends Controller
{
    public function index()
    {
        $salesReturns = SalesReturn::with('customer')->latest()->get();
        return view('business.sales_returns.index', compact('salesReturns'));
    }

    public function create()
    {
        $customers = Customer::all();
        $invoices = Invoice::all();
        $products = Product::all();

        return view('business.sales_returns.create', compact('customers', 'invoices', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:tenant.products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::connection('tenant')->transaction(function () use ($request) {
            $lastReturn = SalesReturn::latest()->first();
            $nextNumber = $lastReturn ? ((int) str_replace('SR-', '', $lastReturn->return_number)) + 1 : 1;
            $returnNumber = 'SR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            $business = auth()->user()->business;
            $customer = Customer::findOrFail($request->customer_id);

            $cgstAmount = 0;
            $sgstAmount = 0;
            $igstAmount = 0;

            if ($business->state === $customer->state) {
                $cgstAmount = $request->tax_amount / 2;
                $sgstAmount = $request->tax_amount / 2;
            } else {
                $igstAmount = $request->tax_amount;
            }
            
            $salesReturn = SalesReturn::create([
                'return_number' => $returnNumber,
                'invoice_id' => $request->invoice_id,
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'igst_amount' => $igstAmount,
                'total_amount' => $request->total_amount,
                'reason' => $request->reason,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $product->gst_percentage ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);

                // Increment Stock since it's a sales return
                $product->increment('stock_quantity', $item['quantity']);
            }

            return redirect()->route('business.sales-returns.index')->with('success', 'Credit Note created successfully!');
        });
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'items', 'invoice']);
        return view('business.sales_returns.show', compact('salesReturn'));
    }

    public function downloadPDF(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'items', 'invoice']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('business.sales_returns.pdf', compact('salesReturn'));
        return $pdf->download('CreditNote-' . $salesReturn->return_number . '.pdf');
    }

    public function destroy(SalesReturn $salesReturn)
    {
        return DB::connection('tenant')->transaction(function () use ($salesReturn) {
            // Restore Stock (undo the return)
            foreach ($salesReturn->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock_quantity', $item->quantity);
                    }
                }
            }

            $salesReturn->items()->delete();
            $salesReturn->delete();

            return redirect()->back()->with('success', 'Credit Note deleted successfully!');
        });
    }
}
