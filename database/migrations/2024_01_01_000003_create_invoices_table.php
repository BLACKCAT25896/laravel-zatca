<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->enum('type', ['tax_invoice', 'simplified_invoice', 'debit_note', 'credit_note'])->default('tax_invoice');
            $table->dateTime('invoice_date');
            $table->dateTime('due_date')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('zatca_uuid')->nullable();
            $table->string('zatca_status')->nullable();
            $table->datetime('zatca_submitted_at')->nullable();
            $table->datetime('zatca_reported_at')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('digital_signature')->nullable();
            $table->string('hash_value')->nullable();
            $table->string('previous_hash')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('invoice_number');
            $table->index('invoice_date');
            $table->index('status');
            $table->index('zatca_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
