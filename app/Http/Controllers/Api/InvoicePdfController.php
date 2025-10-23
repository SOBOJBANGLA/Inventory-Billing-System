<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    /**
     * Generate PDF for invoice.
     */
    public function generate(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'creator', 'invoiceItems.product']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice-{$invoice->id}.pdf");
    }
}
