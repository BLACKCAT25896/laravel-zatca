<?php

namespace App\Http\Controllers;

use App\Models\TaxDeclaration;
use App\Models\Invoice;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaxReportController extends Controller
{
    /**
     * Get tax summary for a business
     */
    public function taxSummary(Request $request): JsonResponse
    {
        $businessId = $request->get('business_id');
        $periodStart = $request->get('period_start');
        $periodEnd = $request->get('period_end');

        $query = Invoice::where('business_id', $businessId)
            ->whereIn('status', ['finalized', 'submitted', 'reported']);

        if ($periodStart) {
            $query->whereDate('invoice_date', '>=', $periodStart);
        }
        if ($periodEnd) {
            $query->whereDate('invoice_date', '<=', $periodEnd);
        }

        $invoices = $query->get();

        $totalAmount = $invoices->sum('total');
        $totalTax = $invoices->sum('tax_amount');
        $totalSubtotal = $invoices->sum('subtotal');
        $invoiceCount = $invoices->count();

        return response()->json([
            'success' => true,
            'data' => [
                'business_id' => $businessId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_invoices' => $invoiceCount,
                'total_amount' => (float) $totalAmount,
                'total_subtotal' => (float) $totalSubtotal,
                'total_tax' => (float) $totalTax,
                'average_tax_rate' => $totalSubtotal > 0 ? ($totalTax / $totalSubtotal * 100) : 0,
            ],
        ]);
    }

    /**
     * Get tax settlement report
     */
    public function taxSettlement(Request $request): JsonResponse
    {
        $businessId = $request->get('business_id');
        $period = $request->get('period', 'monthly');
        $periodStart = Carbon::parse($request->get('period_start'));
        $periodEnd = Carbon::parse($request->get('period_end'));

        $invoices = Invoice::where('business_id', $businessId)
            ->whereBetween('invoice_date', [$periodStart, $periodEnd])
            ->whereIn('status', ['finalized', 'submitted', 'reported'])
            ->get();

        $creditNotes = $invoices->where('type', 'credit_note');
        $debitNotes = $invoices->where('type', 'debit_note');
        $taxInvoices = $invoices->where('type', 'tax_invoice');

        $declaration = TaxDeclaration::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'business_id' => $businessId,
            'declaration_number' => $this->generateDeclarationNumber(),
            'period' => $period,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'total_invoices_amount' => $taxInvoices->sum('total'),
            'total_invoices_count' => $taxInvoices->count(),
            'total_credit_notes_amount' => $creditNotes->sum('total'),
            'total_credit_notes_count' => $creditNotes->count(),
            'total_debit_notes_amount' => $debitNotes->sum('total'),
            'total_debit_notes_count' => $debitNotes->count(),
            'total_taxable_amount' => $invoices->sum('subtotal'),
            'total_tax_amount' => $invoices->sum('tax_amount'),
            'total_tax_payable' => $invoices->sum('tax_amount'),
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax settlement created successfully',
            'data' => $declaration,
        ], 201);
    }

    /**
     * Submit tax declaration to ZATCA
     */
    public function submitDeclaration(TaxDeclaration $declaration): JsonResponse
    {
        try {
            if ($declaration->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft declarations can be submitted',
                ], 422);
            }

            // TODO: Implement ZATCA submission logic
            $declaration->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'zatca_reference' => $this->generateZatcaReference(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax declaration submitted successfully',
                'data' => $declaration,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit declaration',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get tax declaration details
     */
    public function getDeclaration(TaxDeclaration $declaration): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $declaration,
        ]);
    }

    /**
     * Generate unique declaration number
     */
    protected function generateDeclarationNumber(): string
    {
        return 'DECL-' . date('YmdHis') . '-' . rand(1000, 9999);
    }

    /**
     * Generate ZATCA reference
     */
    protected function generateZatcaReference(): string
    {
        return 'ZATCA-' . date('YmdHis') . '-' . rand(10000, 99999);
    }
}
