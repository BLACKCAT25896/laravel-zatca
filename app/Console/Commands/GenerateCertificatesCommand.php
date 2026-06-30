<?php

namespace App\Commands;

use Illuminate\Console\Command;
use App\Services\CryptoService;

class GenerateCertificatesCommand extends Command
{
    protected $signature = 'zatca:generate-certificates
                            {--dn-country=SA : Country code}
                            {--dn-state=Riyadh : State name}
                            {--dn-city=Riyadh : City name}
                            {--dn-org=TestOrg : Organization name}
                            {--dn-ou=IT : Organizational unit}
                            {--dn-cn=test.zatca.local : Common name}
                            {--dn-email=test@zatca.local : Email address}';

    protected $description = 'Generate ZATCA cryptographic certificates';

    public function handle(): int
    {
        $this->info('Generating ZATCA certificates...');

        $dn = [
            'countryName' => $this->option('dn-country'),
            'stateOrProvinceName' => $this->option('dn-state'),
            'localityName' => $this->option('dn-city'),
            'organizationName' => $this->option('dn-org'),
            'organizationalUnitName' => $this->option('dn-ou'),
            'commonName' => $this->option('dn-cn'),
            'emailAddress' => $this->option('dn-email'),
        ];

        $cryptoService = new CryptoService();
        $result = $cryptoService->generateSelfSignedCertificate($dn);

        if ($result['success']) {
            $this->info('✓ Certificates generated successfully');
            $this->info('Certificate: ' . config('zatca.certificate_path'));
            $this->info('Private Key: ' . config('zatca.private_key_path'));
            return 0;
        }

        $this->error('✗ Failed to generate certificates: ' . $result['error']);
        return 1;
    }
}
