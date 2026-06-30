<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Exception;

class InvoiceService
{
    protected CryptoService $cryptoService;
    protected ZatcaService $zatcaService;

    public function __construct()
    {
        $this->cryptoService = new CryptoService();
        $this->zatcaService = new ZatcaService();
    }

    /**
     * Create invoice
     */
    public function createInvoice(array $data): Invoice
    {
        $invoice = Invoice::create([
            'uuid' => Str::uuid(),
            'business_id' => $data['business_id'],
            'supplier_id' => $data['supplier_id'],
            'invoice_number' => $this->generateInvoiceNumber(),
            'type' => $data['type'] ?? 'tax_invoice',
            'invoice_date' => $data['invoice_date'] ?? now(),
            'due_date' => $data['due_date'] ?? null,
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'total' => 0,
            'currency' => $data['currency'] ?? 'SAR',
            'status' => 'draft',
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Add items
        if (!empty($data['items'])) {
            $this->addItems($invoice, $data['items']);
        }

        // Calculate totals
        $this->calculateTotals($invoice);

        return $invoice->fresh();
    }

    /**
     * Add items to invoice
     */
    public function addItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            InvoiceItem::create([
                'uuid' => Str::uuid(),
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'description_ar' => $item['description_ar'] ?? null,
                'sku' => $item['sku'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'],
                'unit' => $item['unit'] ?? 'each',
                'line_subtotal' => ($item['quantity'] ?? 1) * $item['unit_price'],
                'discount_amount' => $item['discount_amount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 15,
                'tax_amount' => 0,
                'line_total' => 0,
            ]);
        }
    }

    /**
     * Calculate invoice totals
     */
    public function calculateTotals(Invoice $invoice): void
    {
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($invoice->items as $item) {
            $lineSubtotal = $item->line_subtotal - ($item->discount_amount ?? 0);
            $taxAmount = $lineSubtotal * ($item->tax_rate / 100);
            
            $subtotal += $lineSubtotal;
            $totalTax += $taxAmount;
            $totalDiscount += $item->discount_amount ?? 0;

            $item->update([
                'tax_amount' => $taxAmount,
                'line_total' => $lineSubtotal + $taxAmount,
            ]);
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $invoice->discount_amount + $totalDiscount,
            'total' => $subtotal + $totalTax,
        ]);
    }

    /**
     * Generate unique invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = config('zatca.invoice_prefix', 'INV');
        $series = config('zatca.invoice_series_start', 1000);
        $count = Invoice::count();
        return $prefix . '-' . str_pad($series + $count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate tax amount
     */
    public function calculateTax(float $amount): float
    {
        if (!config('zatca.vat_enabled')) {
            return 0;
        }
        return $amount * config('zatca.vat_rate', 0.15);
    }

    /**
     * Finalize invoice
     */
    public function finalizeInvoice(Invoice $invoice): Invoice
    {
        $this->calculateTotals($invoice);
        
        // Calculate hash
        $hashValue = $this->cryptoService->calculateHash($invoice);
        $previousHash = $this->getPreviousInvoiceHash($invoice);
        
        $invoice->update([
            'hash_value' => $hashValue,
            'previous_hash' => $previousHash,
            'status' => 'finalized',
        ]);

        return $invoice->fresh();
    }

    /**
     * Get previous invoice hash
     */
    protected function getPreviousInvoiceHash(Invoice $invoice): ?string
    {
        $previousInvoice = Invoice::where('business_id', $invoice->business_id)
            ->where('id', '<', $invoice->id)
            ->orderBy('id', 'desc')
            ->first();

        return $previousInvoice?->hash_value;
    }

    /**
     * Export invoice as XML
     */
    public function exportXml(Invoice $invoice): string
    {
        return $this->zatcaService->generatePhase2Xml($invoice);
    }

    /**
     * Submit invoice to ZATCA
     */
    public function submitToZatca(Invoice $invoice): array
    {
        return $this->zatcaService->submitInvoice($invoice);
    }
}
