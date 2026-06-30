<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('tax_id')->unique();
            $table->string('crn')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->text('address_ar')->nullable();
            $table->string('city');
            $table->string('postal_code');
            $table->string('country', 2)->default('SA');
            $table->text('description')->nullable();
            $table->string('industry_category')->nullable();
            $table->boolean('is_vat_registered')->default(true);
            $table->dateTime('vat_registration_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('tax_id');
            $table->index('crn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
