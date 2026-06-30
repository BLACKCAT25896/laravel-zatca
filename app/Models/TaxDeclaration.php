<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxDeclaration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'business_id',
        'declaration_number',
        'period',
        'period_start',
        'period_end',
        'total_invoices_amount',
        'total_invoices_count',
        'total_credit_notes_amount',
        'total_credit_notes_count',
        'total_debit_notes_amount',
        'total_debit_notes_count',
        'total_taxable_amount',
        'total_tax_amount',
        'total_tax_payable',
        'status',
        'submitted_at',
        'zatca_reference',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'submitted_at' => 'datetime',
        'total_invoices_amount' => 'decimal:2',
        'total_credit_notes_amount' => 'decimal:2',
        'total_debit_notes_amount' => 'decimal:2',
        'total_taxable_amount' => 'decimal:2',
        'total_tax_amount' => 'decimal:2',
        'total_tax_payable' => 'decimal:2',
        'metadata' => 'json',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
