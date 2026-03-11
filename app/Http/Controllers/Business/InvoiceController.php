<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->get();
        return view('business.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $customers = Customer::all();
        $products = Product::where('stock_quantity', '>', 0)->get();
        $selectedCustomerId = $request->query('customer_id');

        return view('business.invoices.create', compact('customers', 'products', 'selectedCustomerId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:tenant.products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::connection('tenant')->transaction(function () use ($request) {
            $lastInvoice = Invoice::latest()->first();
            $nextNumber = $lastInvoice ? ((int) str_replace('INV-', '', $lastInvoice->invoice_number)) + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
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
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'igst_amount' => $igstAmount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => 'paid',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'hsn_number' => $product->hsn_number,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'gst_percentage' => $product->gst_percentage,
                    'total' => $item['total'],
                ]);

                // Deduct Stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            return redirect()->route('business.invoices.index')->with('success', 'Invoice created successfully!');
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        return view('business.invoices.show', compact('invoice'));
    }

    public function downloadPDF(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('business.invoices.pdf', compact('invoice'));
        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function destroy(Invoice $invoice)
    {
        return DB::connection('tenant')->transaction(function () use ($invoice) {
            // Restore Stock
            foreach ($invoice->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Delete Invoice Items (handled by cascade if set, but let's be explicit if not sure)
            // The migration didn't show cascade for invoice_items, but let's check
            $invoice->items()->delete();
            $invoice->delete();

            return redirect()->back()->with('success', 'Invoice deleted and stock restored successfully!');
        });
    }
}
