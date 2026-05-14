<?php

namespace Modules\Subscription\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Models\SubscriptionInvoice;
use Modules\Subscription\Resources\SubscriptionInvoiceResource;

class SubscriptionInvoiceController
{
    /**
     * Get organization invoices
     */
    public function index(Request $request): JsonResponse
    {
        $invoices = SubscriptionInvoice::where('organization_id', $request->user()->organization_id)
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => SubscriptionInvoiceResource::collection($invoices->items()),
            'pagination' => [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
            ],
        ]);
    }

    /**
     * Get specific invoice
     */
    public function show(string $invoiceNumber, Request $request): JsonResponse
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)
            ->where('organization_id', $request->user()->organization_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new SubscriptionInvoiceResource($invoice),
        ]);
    }

    /**
     * Download invoice
     */
    public function download(string $invoiceNumber, Request $request)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)
            ->where('organization_id', $request->user()->organization_id)
            ->firstOrFail();

        // Generate PDF or return download response
        // This is a placeholder - implement based on your PDF generation library
        return response()->json([
            'success' => true,
            'message' => 'Invoice download URL generated',
            'download_url' => 'path/to/invoice/' . $invoice->invoice_number . '.pdf',
        ]);
    }

    /**
     * Get overdue invoices
     */
    public function overdue(Request $request): JsonResponse
    {
        $invoices = SubscriptionInvoice::where('organization_id', $request->user()->organization_id)
            ->overdue()
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubscriptionInvoiceResource::collection($invoices),
        ]);
    }
}
