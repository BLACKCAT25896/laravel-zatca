<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample business
        $business = Business::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Sample Company',
            'name_ar' => 'شركة النموذج',
            'tax_id' => '1234567890',
            'crn' => '1010086670',
            'email' => 'info@sample.com',
            'phone' => '+966501234567',
            'address' => '123 King Fahd Road',
            'address_ar' => '123 شارع الملك فهد',
            'city' => 'Riyadh',
            'postal_code' => '11111',
            'country' => 'SA',
            'description' => 'Sample business for ZATCA testing',
            'industry_category' => 'Technology',
            'is_vat_registered' => true,
            'vat_registration_date' => now()->subYear(),
            'bank_name' => 'Saudi National Bank',
            'bank_account' => '1234567890',
            'bank_iban' => 'SA0320000001234567890123',
            'status' => 'active',
        ]);

        // Create sample suppliers
        $suppliers = [
            [
                'name' => 'Customer One',
                'name_ar' => 'العميل الأول',
                'email' => 'customer1@example.com',
                'phone' => '+966501111111',
                'type' => 'customer',
            ],
            [
                'name' => 'Customer Two',
                'name_ar' => 'العميل الثاني',
                'email' => 'customer2@example.com',
                'phone' => '+966502222222',
                'type' => 'customer',
            ],
            [
                'name' => 'Vendor One',
                'name_ar' => 'المورد الأول',
                'email' => 'vendor1@example.com',
                'phone' => '+966503333333',
                'type' => 'vendor',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'business_id' => $business->id,
                'tax_id' => fake()->numerify('############'),
                'address' => fake()->address(),
                'city' => 'Riyadh',
                'country' => 'SA',
                'credit_limit' => 50000,
                'current_balance' => 0,
                'status' => 'active',
                ...$supplierData,
            ]);
        }

        // Create sample invoices
        $invoiceCount = 5;
        for ($i = 0; $i < $invoiceCount; $i++) {
            $supplier = Supplier::where('business_id', $business->id)->random();
            
            $subtotal = fake()->numberBetween(5000, 50000);
            $taxAmount = $subtotal * 0.15;
            $total = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'business_id' => $business->id,
                'supplier_id' => $supplier->id,
                'invoice_number' => 'INV-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'type' => 'tax_invoice',
                'invoice_date' => now()->subDays(fake()->numberBetween(1, 30)),
                'due_date' => now()->addDays(30),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total' => $total,
                'currency' => 'SAR',
                'status' => fake()->randomElement(['draft', 'finalized']),
                'description' => 'Sample invoice for testing',
            ]);

            // Add invoice items
            $itemCount = fake()->numberBetween(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $quantity = fake()->numberBetween(1, 5);
                $unitPrice = fake()->numberBetween(1000, 10000);
                $lineSubtotal = $quantity * $unitPrice;
                $lineTax = $lineSubtotal * 0.15;

                InvoiceItem::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'description' => 'Sample Item ' . ($j + 1),
                    'description_ar' => 'العنصر رقم ' . ($j + 1),
                    'sku' => 'SKU-' . fake()->numberBetween(1000, 9999),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit' => 'each',
                    'line_subtotal' => $lineSubtotal,
                    'discount_amount' => 0,
                    'tax_rate' => 15,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineSubtotal + $lineTax,
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info("Created Business: {$business->name}");
        $this->command->info('Created 3 Suppliers and 5 Invoices');
    }
}
