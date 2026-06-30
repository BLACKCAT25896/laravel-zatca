<?php

namespace App\Listeners;

use App\Events\InvoiceFinalized;

class SendInvoiceFinalizedNotification
{
    public function handle(InvoiceFinalized $event): void
    {
        // Send notification to business owner
        // Email, SMS, or in-app notification
    }
}
