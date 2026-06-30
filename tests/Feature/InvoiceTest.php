<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Supplier;
use App\Models\Invoice;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    protected Business $business;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = Business::factory()->create();
        $this->supplier = Supplier::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_can_create_invoice()
    {
        $response = $this->postJson('/api/invoices', [
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'tax_invoice',
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 2,
                    'unit_price' => 100,
                    'tax_rate' => 15,
                ],
            ],
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'draft');
    }

    public function test_can_finalize_invoice()
    {
        $invoice = Invoice::factory()->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/invoices/{$invoice->id}/finalize");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'finalized');
    }

    public function test_can_get_invoice_xml()
    {
        $invoice = Invoice::factory()->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->getJson("/api/invoices/{$invoice->id}/xml");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_can_get_invoice_qr_code()
    {
        $invoice = Invoice::factory()->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->getJson("/api/invoices/{$invoice->id}/qrcode");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_can_list_invoices()
    {
        Invoice::factory()->count(3)->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200);
    }

    public function test_can_delete_draft_invoice()
    {
        $invoice = Invoice::factory()->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);

        $response = $this->deleteJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }
}
