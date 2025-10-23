<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['customer', 'creator', 'invoiceItems.product']);

        // Filter by customer
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $total = 0;
            $invoiceItems = [];

            // Calculate total and prepare invoice items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check if enough stock is available
                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}",
                    ], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $invoiceItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Create invoice
            $invoice = Invoice::create([
                'customer_id' => $request->customer_id,
                'total' => $total,
                'created_by' => auth()->id(),
            ]);

            // Create invoice items and update product quantities
            foreach ($invoiceItems as $item) {
                $invoice->invoiceItems()->create($item);
                
                // Deduct stock
                Product::where('id', $item['product_id'])
                    ->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully.',
                'data' => $invoice->load(['customer', 'invoiceItems.product']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['customer', 'creator', 'invoiceItems.product']);

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Restore stock quantities
            foreach ($invoice->invoiceItems as $item) {
                Product::where('id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }

            $invoice->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF for invoice.
     */
    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'creator', 'invoiceItems.product']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice-{$invoice->id}.pdf");
    }
}
