<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $purchaseReturns = PurchaseReturn::latest()->get();
        return view('business.purchase_returns.index', compact('purchaseReturns'));
    }

    public function create()
    {
        $expenses = Expense::all();
        $products = Product::all();

        return view('business.purchase_returns.create', compact('expenses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::connection('tenant')->transaction(function () use ($request) {
            $lastReturn = PurchaseReturn::latest()->first();
            $nextNumber = $lastReturn ? ((int) str_replace('PR-', '', $lastReturn->return_number)) + 1 : 1;
            $returnNumber = 'PR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            $purchaseReturn = PurchaseReturn::create([
                'return_number' => $returnNumber,
                'vendor_name' => $request->vendor_name,
                'expense_id' => $request->expense_id,
                'date' => $request->date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'cgst_amount' => $request->tax_amount / 2, // Assumption for simplicity as we don't know vendor state
                'sgst_amount' => $request->tax_amount / 2,
                'igst_amount' => 0,
                'total_amount' => $request->total_amount,
                'reason' => $request->reason,
            ]);

            foreach ($request->items as $item) {
                // If the product is selected from dropdown, decrement stock.
                // Since our items allow string product_name, we check if there's a matching product to adjust stock.
                $product = Product::where('name', $item['product_name'])->first();
                if ($product) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);
            }

            return redirect()->route('business.purchase-returns.index')->with('success', 'Debit Note created successfully!');
        });
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['items', 'expense']);
        return view('business.purchase_returns.show', compact('purchaseReturn'));
    }

    public function downloadPDF(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['items', 'expense']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('business.purchase_returns.pdf', compact('purchaseReturn'));
        return $pdf->download('DebitNote-' . $purchaseReturn->return_number . '.pdf');
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        return DB::connection('tenant')->transaction(function () use ($purchaseReturn) {
            // Restore Stock
            foreach ($purchaseReturn->items as $item) {
                $product = Product::where('name', $item->product_name)->first();
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            $purchaseReturn->items()->delete();
            $purchaseReturn->delete();

            return redirect()->back()->with('success', 'Debit Note deleted successfully!');
        });
    }
}
