<?php

namespace Tests\Feature;

use App\Models\Business;
use Tests\TestCase;

class BusinessTest extends TestCase
{
    public function test_can_create_business()
    {
        $response = $this->postJson('/api/businesses', [
            'name' => 'Test Business',
            'tax_id' => '1234567890',
            'email' => 'test@business.com',
            'phone' => '+966501234567',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'postal_code' => '11111',
            'country' => 'SA',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.tax_id', '1234567890');
    }

    public function test_can_list_businesses()
    {
        Business::factory()->count(5)->create();

        $response = $this->getJson('/api/businesses');

        $response->assertStatus(200);
    }

    public function test_can_get_business_details()
    {
        $business = Business::factory()->create();

        $response = $this->getJson("/api/businesses/{$business->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $business->id);
    }

    public function test_can_update_business()
    {
        $business = Business::factory()->create();

        $response = $this->putJson("/api/businesses/{$business->id}", [
            'name' => 'Updated Business Name',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Updated Business Name');
    }

    public function test_can_delete_business()
    {
        $business = Business::factory()->create();

        $response = $this->deleteJson("/api/businesses/{$business->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }
}
