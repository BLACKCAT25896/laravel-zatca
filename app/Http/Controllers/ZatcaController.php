<?php

namespace App\Http\Controllers;

use App\Services\ZatcaService;
use App\Services\CryptoService;
use Illuminate\Http\JsonResponse;

class ZatcaController extends Controller
{
    protected ZatcaService $zatcaService;
    protected CryptoService $cryptoService;

    public function __construct(
        ZatcaService $zatcaService,
        CryptoService $cryptoService
    ) {
        $this->zatcaService = $zatcaService;
        $this->cryptoService = $cryptoService;
    }

    /**
     * Check ZATCA configuration
     */
    public function checkConfiguration(): JsonResponse
    {
        $validation = $this->zatcaService->validateConfiguration();

        return response()->json([
            'success' => $validation['valid'],
            'message' => $validation['valid'] ? 'ZATCA configuration is valid' : 'ZATCA configuration has errors',
            'validation' => $validation,
        ], $validation['valid'] ? 200 : 422);
    }

    /**
     * Get certificate details
     */
    public function getCertificateDetails(): JsonResponse
    {
        try {
            $details = $this->cryptoService->getCertificateDetails();

            return response()->json([
                'success' => true,
                'data' => $details,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get certificate details',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
