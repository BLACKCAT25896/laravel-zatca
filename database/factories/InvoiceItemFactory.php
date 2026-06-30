<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->numberBetween(100, 5000);
        $lineSubtotal = $quantity * $unitPrice;
        $taxRate = 15;
        $taxAmount = $lineSubtotal * ($taxRate / 100);

        return [
            'uuid' => fake()->uuid(),
            'invoice_id' => 1,
            'description' => fake()->sentence(3),
            'description_ar' => fake()->sentence(3),
            'sku' => fake()->sku(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit' => 'each',
            'line_subtotal' => $lineSubtotal,
            'discount_amount' => fake()->numberBetween(0, 1000),
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'line_total' => $lineSubtotal + $taxAmount,
        ];
    }
}
