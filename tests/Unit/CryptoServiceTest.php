<?php

namespace Tests\Unit;

use App\Services\CryptoService;
use Tests\TestCase;

class CryptoServiceTest extends TestCase
{
    protected CryptoService $cryptoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cryptoService = new CryptoService();
    }

    public function test_can_generate_self_signed_certificate()
    {
        $result = $this->cryptoService->generateSelfSignedCertificate();

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['certificate']);
        $this->assertNotEmpty($result['private_key']);
    }

    public function test_can_encode_and_decode_data()
    {
        $data = 'test data to encode';

        $encoded = $this->cryptoService->encode($data);
        $decoded = $this->cryptoService->decode($encoded);

        $this->assertEquals($data, $decoded);
    }
}
