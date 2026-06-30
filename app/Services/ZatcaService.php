<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ZatcaService
{
    protected string $apiUrl;
    protected string $mode;
    protected string $environment;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('zatca.api_url');
        $this->mode = config('zatca.mode');
        $this->environment = config('zatca.environment');
        $this->timeout = config('zatca.timeout', 30);
    }

    /**
     * Generate Phase 1 XML Invoice
     */
    public function generatePhase1Xml(Invoice $invoice): string
    {
        $xmlGenerator = new XmlInvoiceGenerator($invoice);
        return $xmlGenerator->generate();
    }

    /**
     * Generate Phase 2 XML Invoice with QR Code
     */
    public function generatePhase2Xml(Invoice $invoice): string
    {
        $xmlGenerator = new XmlInvoiceGenerator($invoice);
        $xml = $xmlGenerator->generate();
        
        // Generate QR Code
        $qrCode = $this->generateQrCode($invoice);
        $invoice->update(['qr_code' => $qrCode]);
        
        return $xml;
    }

    /**
     * Generate QR Code for invoice
     */
    public function generateQrCode(Invoice $invoice): string
    {
        $qrGenerator = new QrCodeGenerator($invoice);
        return $qrGenerator->generate();
    }

    /**
     * Sign invoice XML
     */
    public function signInvoice(Invoice $invoice, string $xml): string
    {
        $signer = new CryptoService();
        $signature = $signer->sign($xml);
        
        // Store signature
        $invoice->signature()->create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'signature' => $signature,
            'algorithm' => config('zatca.hash_algorithm'),
            'signed_at' => now(),
        ]);
        
        return $signature;
    }

    /**
     * Submit invoice to ZATCA
     */
    public function submitInvoice(Invoice $invoice): array
    {
        try {
            $xml = $this->generatePhase2Xml($invoice);
            $signature = $this->signInvoice($invoice, $xml);
            
            $payload = [
                'invoice' => base64_encode($xml),
                'invoiceHash' => hash('sha256', $xml),
            ];
            
            $response = Http::timeout($this->timeout)
                ->withBasicAuth(
                    config('zatca.username'),
                    config('zatca.password')
                )
                ->post($this->apiUrl . '/invoices/reporting/single', $payload);
            
            if ($response->successful()) {
                $data = $response->json();
                $invoice->update([
                    'zatca_uuid' => $data['uuid'] ?? null,
                    'zatca_status' => 'submitted',
                    'zatca_submitted_at' => now(),
                    'digital_signature' => $signature,
                ]);
                
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }
            
            throw new Exception('ZATCA submission failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ZATCA submission error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Report invoice to ZATCA
     */
    public function reportInvoice(Invoice $invoice): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withBasicAuth(
                    config('zatca.username'),
                    config('zatca.password')
                )
                ->post($this->apiUrl . '/invoices/reporting/single', [
                    'invoiceHash' => $invoice->hash_value,
                    'previousInvoiceHash' => $invoice->previous_hash,
                ]);
            
            if ($response->successful()) {
                $invoice->update([
                    'zatca_status' => 'reported',
                    'zatca_reported_at' => now(),
                ]);
                
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }
            
            throw new Exception('ZATCA reporting failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ZATCA reporting error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate ZATCA configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        
        if (empty(config('zatca.username'))) {
            $errors[] = 'ZATCA_USERNAME is not configured';
        }
        if (empty(config('zatca.password'))) {
            $errors[] = 'ZATCA_PASSWORD is not configured';
        }
        if (empty(config('zatca.certificate_path')) || !file_exists(config('zatca.certificate_path'))) {
            $errors[] = 'ZATCA certificate file not found';
        }
        if (empty(config('zatca.private_key_path')) || !file_exists(config('zatca.private_key_path'))) {
            $errors[] = 'ZATCA private key file not found';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
