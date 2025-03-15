<?php

namespace Tests\Domain\Services;

use PHPUnit\Framework\TestCase;
use Domain\Services\QrCodeService;
use Domain\ValueObjects\QrCode\{
    QrCodeText,
    QrCodeSize,
    QrCodeLevel,
    QrCodeMargin
};
use Domain\ValueObjects\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrCodeServiceTest extends TestCase
{
    private QrCodeService $service;

    protected function setUp(): void
    {
        $this->service = new QrCodeService();
    }

    public function testGenerateQrCode(): void
    {
        $qrCode = $this->service->generateQrCode('test data');

        $this->assertInstanceOf(\Endroid\QrCode\QrCode::class, $qrCode);
        $this->assertEquals('test data', $qrCode->getData());
    }
} 