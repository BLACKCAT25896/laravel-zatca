<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'invoice_id',
        'signature',
        'algorithm',
        'public_key',
        'certificate',
        'signed_at',
        'metadata',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
