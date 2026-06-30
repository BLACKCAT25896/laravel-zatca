<?php

namespace Tests\Unit;

use App\Services\XmlInvoiceGenerator;
use App\Models\Business;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Tests\TestCase;

class XmlInvoiceGeneratorTest extends TestCase
{
    public function test_can_generate_valid_xml()
    {
        $business = Business::factory()->create();
        $supplier = Supplier::factory()->create(['business_id' => $business->id]);
        
        $invoice = Invoice::factory()->create([
            'business_id' => $business->id,
            'supplier_id' => $supplier->id,
        ]);

        InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

        $generator = new XmlInvoiceGenerator($invoice);
        $xml = $generator->generate();

        $this->assertStringContainsString('<?xml', $xml);
        $this->assertStringContainsString('Invoice', $xml);
        $this->assertStringContainsString($invoice->invoice_number, $xml);
    }

    public function test_xml_contains_required_elements()
    {
        $business = Business::factory()->create();
        $supplier = Supplier::factory()->create(['business_id' => $business->id]);
        
        $invoice = Invoice::factory()->create([
            'business_id' => $business->id,
            'supplier_id' => $supplier->id,
        ]);

        InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

        $generator = new XmlInvoiceGenerator($invoice);
        $xml = $generator->generate();

        $this->assertStringContainsString('UBLVersionID', $xml);
        $this->assertStringContainsString('ProfileID', $xml);
        $this->assertStringContainsString('InvoiceTypeCode', $xml);
        $this->assertStringContainsString('LegalMonetaryTotal', $xml);
    }
}
