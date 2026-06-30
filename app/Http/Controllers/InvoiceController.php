<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Business;
use App\Models\Supplier;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use App\Services\ZatcaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected ZatcaService $zatcaService;

    public function __construct(
        InvoiceService $invoiceService,
        ZatcaService $zatcaService
    ) {
        $this->invoiceService = $invoiceService;
        $this->zatcaService = $zatcaService;
    }

    /**
     * List all invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::query();

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->paginate($request->get('per_page', 15));

        return response()->json(InvoiceResource::collection($invoices));
    }

    /**
     * Create invoice
     */
    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => new InvoiceResource($invoice),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get invoice details
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json(new InvoiceResource($invoice));
    }

    /**
     * Update invoice
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        try {
            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft invoices can be updated',
                ], 422);
            }

            $invoice->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => new InvoiceResource($invoice),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Finalize invoice
     */
    public function finalize(Invoice $invoice): JsonResponse
    {
        try {
            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft invoices can be finalized',
                ], 422);
            }

            $this->invoiceService->finalizeInvoice($invoice);

            return response()->json([
                'success' => true,
                'message' => 'Invoice finalized successfully',
                'data' => new InvoiceResource($invoice),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to finalize invoice',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get invoice XML
     */
    public function getXml(Invoice $invoice): JsonResponse
    {
        try {
            $xml = $this->zatcaService->generatePhase2Xml($invoice);

            return response()->json([
                'success' => true,
                'data' => $xml,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate XML',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get invoice QR Code
     */
    public function getQrCode(Invoice $invoice): JsonResponse
    {
        try {
            $qrCode = $this->zatcaService->generateQrCode($invoice);

            return response()->json([
                'success' => true,
                'data' => $qrCode,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Submit invoice to ZATCA
     */
    public function submitToZatca(Invoice $invoice): JsonResponse
    {
        try {
            if ($invoice->status !== 'finalized') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice must be finalized before submission',
                ], 422);
            }

            $result = $this->zatcaService->submitInvoice($invoice);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Invoice submitted to ZATCA' : 'Failed to submit invoice',
                'data' => $result,
            ], $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting invoice',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete invoice
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        try {
            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft invoices can be deleted',
                ], 422);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
