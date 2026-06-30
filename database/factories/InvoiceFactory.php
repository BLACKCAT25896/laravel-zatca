<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(1000, 50000);
        $taxRate = 0.15;
        $taxAmount = $subtotal * $taxRate;

        return [
            'uuid' => fake()->uuid(),
            'business_id' => 1,
            'supplier_id' => 1,
            'invoice_number' => 'INV-' . str_pad(fake()->unique()->numberBetween(1000, 999999), 6, '0', STR_PAD_LEFT),
            'type' => fake()->randomElement(['tax_invoice', 'simplified_invoice']),
            'invoice_date' => fake()->dateTimeThisMonth(),
            'due_date' => fake()->dateTimeThisMonth(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => fake()->numberBetween(0, 5000),
            'total' => $subtotal + $taxAmount,
            'currency' => 'SAR',
            'status' => fake()->randomElement(['draft', 'finalized']),
            'description' => fake()->sentence(),
        ];
    }
}
