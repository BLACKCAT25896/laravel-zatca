<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZatcaService;

class ZatcaValidateCommand extends Command
{
    protected $signature = 'zatca:validate';
    protected $description = 'Validate ZATCA configuration';

    public function handle(): int
    {
        $this->info('Validating ZATCA configuration...');

        $zatcaService = new ZatcaService();
        $validation = $zatcaService->validateConfiguration();

        if ($validation['valid']) {
            $this->info('✓ ZATCA configuration is valid');
            return 0;
        }

        $this->error('✗ ZATCA configuration has errors:');
        foreach ($validation['errors'] as $error) {
            $this->error('  - ' . $error);
        }

        return 1;
    }
}
