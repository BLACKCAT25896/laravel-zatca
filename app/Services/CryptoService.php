<?php

namespace App\Services;

use App\Models\Invoice;
use Exception;

class CryptoService
{
    protected string $algorithm = 'sha256';
    protected string $privateKeyPath;
    protected string $certificatePath;

    public function __construct()
    {
        $this->privateKeyPath = config('zatca.private_key_path');
        $this->certificatePath = config('zatca.certificate_path');
    }

    /**
     * Sign data with private key
     */
    public function sign(string $data): string
    {
        try {
            $privateKey = $this->getPrivateKey();
            $signature = '';
            
            openssl_sign($data, $signature, $privateKey, 'sha256WithRSAEncryption');
            
            return base64_encode($signature);
        } catch (Exception $e) {
            throw new Exception('Failed to sign data: ' . $e->getMessage());
        }
    }

    /**
     * Verify signature
     */
    public function verify(string $data, string $signature): bool
    {
        try {
            $certificate = $this->getCertificate();
            $decodedSignature = base64_decode($signature);
            
            $result = openssl_verify($data, $decodedSignature, $certificate, 'sha256WithRSAEncryption');
            
            return $result === 1;
        } catch (Exception $e) {
            throw new Exception('Failed to verify signature: ' . $e->getMessage());
        }
    }

    /**
     * Calculate SHA-256 hash
     */
    public function calculateHash(Invoice $invoice): string
    {
        $data = $this->prepareHashData($invoice);
        return hash('sha256', $data);
    }

    /**
     * Prepare data for hashing
     */
    protected function prepareHashData(Invoice $invoice): string
    {
        $data = [
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date->toDateTimeString(),
            'total' => $invoice->total,
            'tax_amount' => $invoice->tax_amount,
        ];

        return json_encode($data);
    }

    /**
     * Generate certificate request
     */
    public function generateCertificateRequest(array $dn): string
    {
        try {
            $config = [
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            $privateKey = openssl_pkey_new($config);
            $csr = openssl_csr_new($dn, $privateKey);

            openssl_pkey_export($privateKey, $privKeyOut);
            openssl_csr_export($csr, $csrOut);

            $this->savePrivateKey($privKeyOut);

            return $csrOut;
        } catch (Exception $e) {
            throw new Exception('Failed to generate certificate request: ' . $e->getMessage());
        }
    }

    /**
     * Generate self-signed certificate
     */
    public function generateSelfSignedCertificate(array $dn = []): array
    {
        try {
            if (empty($dn)) {
                $dn = [
                    'countryName' => 'SA',
                    'stateOrProvinceName' => 'Riyadh',
                    'localityName' => 'Riyadh',
                    'organizationName' => 'Test Organization',
                    'organizationalUnitName' => 'IT',
                    'commonName' => 'test.zatca.local',
                    'emailAddress' => 'test@zatca.local',
                ];
            }

            $config = [
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            $privateKey = openssl_pkey_new($config);
            $csr = openssl_csr_new($dn, $privateKey);
            $x509 = openssl_csr_sign($csr, null, $privateKey, 365);

            openssl_x509_export($x509, $certOut);
            openssl_pkey_export($privateKey, $privKeyOut);

            $this->saveCertificate($certOut);
            $this->savePrivateKey($privKeyOut);

            return [
                'certificate' => $certOut,
                'private_key' => $privKeyOut,
                'success' => true,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to generate certificate: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get private key resource
     */
    protected function getPrivateKey()
    {
        if (!file_exists($this->privateKeyPath)) {
            throw new Exception('Private key file not found: ' . $this->privateKeyPath);
        }

        $keyContent = file_get_contents($this->privateKeyPath);
        $privateKey = openssl_pkey_get_private($keyContent);

        if ($privateKey === false) {
            throw new Exception('Invalid private key');
        }

        return $privateKey;
    }

    /**
     * Get certificate resource
     */
    protected function getCertificate()
    {
        if (!file_exists($this->certificatePath)) {
            throw new Exception('Certificate file not found: ' . $this->certificatePath);
        }

        $certContent = file_get_contents($this->certificatePath);
        $certificate = openssl_x509_read($certContent);

        if ($certificate === false) {
            throw new Exception('Invalid certificate');
        }

        return $certificate;
    }

    /**
     * Save private key to file
     */
    protected function savePrivateKey(string $key): void
    {
        $dir = dirname($this->privateKeyPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->privateKeyPath, $key);
        chmod($this->privateKeyPath, 0600);
    }

    /**
     * Save certificate to file
     */
    protected function saveCertificate(string $cert): void
    {
        $dir = dirname($this->certificatePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->certificatePath, $cert);
        chmod($this->certificatePath, 0644);
    }

    /**
     * Get certificate details
     */
    public function getCertificateDetails(): array
    {
        try {
            $cert = $this->getCertificate();
            return openssl_x509_parse($cert);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Encode to base64
     */
    public function encode(string $data): string
    {
        return base64_encode($data);
    }

    /**
     * Decode from base64
     */
    public function decode(string $data): string
    {
        return base64_decode($data);
    }
}
