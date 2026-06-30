<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'name_ar',
        'tax_id',
        'crn',
        'email',
        'phone',
        'address',
        'address_ar',
        'city',
        'postal_code',
        'country',
        'description',
        'industry_category',
        'is_vat_registered',
        'vat_registration_date',
        'bank_name',
        'bank_account',
        'bank_iban',
        'status',
        'metadata',
    ];

    protected $casts = [
        'is_vat_registered' => 'boolean',
        'vat_registration_date' => 'datetime',
        'metadata' => 'json',
    ];

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function taxDeclarations(): HasMany
    {
        return $this->hasMany(TaxDeclaration::class);
    }
}
