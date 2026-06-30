<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceSubmittedToZatca
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public function __construct(public Invoice $invoice)
    {}
}
