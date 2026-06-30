<?php

namespace App\Services;

use App\Models\Invoice;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeGenerator
{
    protected Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Generate QR Code
     */
    public function generate(): string
    {
        $qrData = $this->prepareQrData();
        
        $options = new QROptions([
            'version' => config('zatca.qr_version', 2),
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true,
        ]);
        
        $qrCode = new QRCode($options);
        return $qrCode->render($qrData);
    }

    /**
     * Generate QR Code as SVG
     */
    public function generateSvg(): string
    {
        $qrData = $this->prepareQrData();
        
        $options = new QROptions([
            'version' => config('zatca.qr_version', 2),
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
        ]);
        
        $qrCode = new QRCode($options);
        return $qrCode->render($qrData);
    }

    /**
     * Prepare data for QR Code encoding
     * ZATCA Phase 2 QR Code format
     */
    protected function prepareQrData(): string
    {
        // QR Code Data Structure for ZATCA Phase 2
        $data = [
            '1' => $this->invoice->business->name,                    // Seller name
            '2' => $this->invoice->business->tax_id,                  // VAT number
            '3' => $this->invoice->invoice_date->toDateTimeString(), // Invoice date and time
            '4' => number_format($this->invoice->total, 2, '.', ''), // Invoice total
            '5' => number_format($this->invoice->tax_amount, 2, '.', ''), // Tax amount
            '6' => $this->invoice->hash_value ?? '',                  // Invoice hash
        ];
        
        return json_encode($data);
    }

    /**
     * Get QR Code Base64 data URL
     */
    public function getDataUrl(): string
    {
        $png = $this->generate();
        return 'data:image/png;base64,' . $png;
    }
}
