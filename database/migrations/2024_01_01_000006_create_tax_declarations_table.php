<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_declarations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('declaration_number')->unique();
            $table->enum('period', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_invoices_amount', 15, 2)->default(0);
            $table->integer('total_invoices_count')->default(0);
            $table->decimal('total_credit_notes_amount', 15, 2)->default(0);
            $table->integer('total_credit_notes_count')->default(0);
            $table->decimal('total_debit_notes_amount', 15, 2)->default(0);
            $table->integer('total_debit_notes_count')->default(0);
            $table->decimal('total_taxable_amount', 15, 2)->default(0);
            $table->decimal('total_tax_amount', 15, 2)->default(0);
            $table->decimal('total_tax_payable', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->datetime('submitted_at')->nullable();
            $table->string('zatca_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('period_start');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_declarations');
    }
};
