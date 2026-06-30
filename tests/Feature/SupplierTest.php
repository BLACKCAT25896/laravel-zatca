<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Supplier;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    protected Business $business;

    protected function setUp(): void
    {
        parent::setUp();
        $this->business = Business::factory()->create();
    }

    public function test_can_create_supplier()
    {
        $response = $this->postJson('/api/suppliers', [
            'business_id' => $this->business->id,
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'type' => 'vendor',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Test Supplier');
    }

    public function test_can_list_suppliers()
    {
        Supplier::factory(3)->create(['business_id' => $this->business->id]);

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(200);
    }

    public function test_can_update_supplier()
    {
        $supplier = Supplier::factory()->create(['business_id' => $this->business->id]);

        $response = $this->putJson("/api/suppliers/{$supplier->id}", [
            'name' => 'Updated Supplier Name',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Updated Supplier Name');
    }
}
