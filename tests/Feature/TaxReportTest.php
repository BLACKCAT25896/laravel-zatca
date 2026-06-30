<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Supplier;
use App\Models\Invoice;
use Tests\TestCase;

class TaxReportTest extends TestCase
{
    protected Business $business;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->business = Business::factory()->create();
        $this->supplier = Supplier::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_can_get_tax_summary()
    {
        Invoice::factory(5)->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'finalized',
        ]);

        $response = $this->getJson("/api/reports/tax-summary?business_id={$this->business->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.total_invoices', 5);
    }

    public function test_can_create_tax_settlement()
    {
        Invoice::factory(3)->create([
            'business_id' => $this->business->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'finalized',
            'type' => 'tax_invoice',
        ]);

        $response = $this->postJson('/api/reports/tax-settlement', [
            'business_id' => $this->business->id,
            'period' => 'monthly',
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'draft');
    }
}
