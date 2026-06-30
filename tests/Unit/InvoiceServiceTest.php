<?php

namespace Tests\Unit;

use App\Services\InvoiceService;
use App\Models\Business;
use App\Models\Supplier;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    protected InvoiceService $invoiceService;
    protected Business $business;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = new InvoiceService();
        $this->business = Business::factory()->create();
        $this->supplier = Supplier::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_can_generate_unique_invoice_numbers()
    {
        $invoice1 = $this->invoiceService->createInvoice([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['description' => 'Item 1', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $invoice2 = $this->invoiceService->createInvoice([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['description' => 'Item 2', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $this->assertNotEquals($invoice1->invoice_number, $invoice2->invoice_number);
    }

    public function test_tax_calculation_is_accurate()
    {
        $invoice = $this->invoiceService->createInvoice([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['description' => 'Item 1', 'quantity' => 2, 'unit_price' => 1000, 'tax_rate' => 15],
            ],
        ]);

        $expectedTax = 2000 * 0.15;
        $this->assertEquals($expectedTax, $invoice->tax_amount);
    }

    public function test_can_finalize_invoice()
    {
        $invoice = $this->invoiceService->createInvoice([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['description' => 'Item 1', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $this->assertEquals('draft', $invoice->status);

        $finalized = $this->invoiceService->finalizeInvoice($invoice);
        $this->assertEquals('finalized', $finalized->status);
        $this->assertNotNull($finalized->hash_value);
    }
}
